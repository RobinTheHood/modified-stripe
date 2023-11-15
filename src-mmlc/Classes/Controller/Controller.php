<?php

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Controller;

use Exception;
use RobinTheHood\Stripe\Classes\Constants;
use RobinTheHood\Stripe\Classes\Framework\AbstractController;
use RobinTheHood\Stripe\Classes\Framework\Database;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\RedirectResponse;
use RobinTheHood\Stripe\Classes\Framework\Request;
use RobinTheHood\Stripe\Classes\Framework\Response;
use RobinTheHood\Stripe\Classes\Repository;
use RobinTheHood\Stripe\Classes\Session as PhpSession;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\StripeService;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Event;
use Stripe\Stripe;

/**
 * The AbstractController can automatically forward requests to methods beginning with the invoke prefix via the ?action=
 * query parameter in the URL. If action is empty or not set, invokeIndex() is called by default.
 * The entry point of this class is in file shop-root/rth_stripe.php
 */
class Controller extends AbstractController
{
    private StripeConfiguration $config;

    private DIContainer $container;

    public function __construct(DIContainer $container)
    {
        parent::__construct();
        $this->config    = new StripeConfiguration(Constants::MODULE_PAYMENT_NAME);
        $this->container = $container;
    }

    protected function invokeIndex(Request $request): Response
    {
        return new Response('There is nothing to do');
    }

    /**
     * This method is called after the customer clicks on the "Buy Button" on step 3 (checkout_confirmation.php)
     *
     * @see /includes/modules/payment/payment_rth_stripe.php $form_action_url
     * @link https://stripe.com/docs/checkout/quickstart
     * @link https://stripe.com/docs/payments/checkout/custom-success-page
     */
    protected function invokeCheckout(): Response
    {
        $domain = HTTPS_SERVER;

        /**
         * We need to save the current PHP session, as it may have already expired if the customer takes a long time
         * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
         * placed in the shop.
         */
        $phpSession = $this->container->get(PhpSession::class);
        $sessionId  = $phpSession->save();

        $order = $phpSession->getOrder();
        if (!$order) {
            die('Can not create a Stripe session because we have no order Obj');
        }

        Stripe::setApiKey($this->getSecretKey());

        /**
         * TODO: Use reasonable defaults per language.
         */
        $name        = parse_multi_language_value($this->config->checkoutTitle, $_SESSION['language_code']) ?: 'title';
        $description = parse_multi_language_value($this->config->checkoutDesc, $_SESSION['language_code']) ?: 'description';

        $priceData = [
            'currency'     => 'eur',
            'unit_amount'  => $order->getTotal() * 100, // Value in Cent
            'product_data' => [
                'name'        => $name,
                'description' => $description,
            ]
        ];


        /**
         * Creates a Stripe checkout session object. Don't confuse it with a PHP session. Both use the same name.
         *
         * @link https://stripe.com/docs/api/checkout/sessions/object
         */
        $checkoutSession = StripeSession::create([
            'line_items'          => [[
                'price_data' => $priceData,
                'quantity'   => 1,
            ]],
            'client_reference_id' => $sessionId,
            'mode'                => 'payment',
            'success_url'         => $domain . '/rth_stripe.php?action=success&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'          => $domain . '/rth_stripe.php?action=cancel',
            'expires_at'          => time() + (3600 * 0.5) // Configured to expire after 30 minutes
        ]);

        return new RedirectResponse($checkoutSession->url);
    }

    protected function invokeSuccess(): Response
    {
        require_once DIR_WS_FUNCTIONS . 'sessions.php';
        include_once DIR_WS_MODULES . 'set_session_and_cookie_parameters.php';

        $stripe = new \Stripe\StripeClient($this->getSecretKey());

        try {
            $session      = $stripe->checkout->sessions->retrieve($_GET['session_id']);
            $phpSessionId = $session->client_reference_id;

            $phpSession = $this->container->get(PhpSession::class);
            $phpSession->load($phpSessionId);
            $_SESSION['rth_stripe_status'] = 'success';

            // TODO: Check if the order was realy paid, if possible
            // TODO: Load the php session if the payment process took too long

            return new RedirectResponse('/checkout_process.php');
        } catch (Exception $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 500);
        }
    }

    /**
     * //TODOO: See Issue #42 - Add option to keep temporary order - for more options of cancelation
     *
     * @see modules/payment/payment_rth_stripe.php::pre_confirmation_check() to learn how the temporary order is deleted
     */
    public function invokeCancel(): Response
    {
        return new RedirectResponse('/checkout_confirmation.php');
    }

    /**
     * // TODO: move this to its own Webhook Controller
     *
     * The receiveHook action was registered as a WebHook with Stripe, so we can receive it in this method.
     */
    protected function invokeReceiveHook(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->getServer('HTTP_STRIPE_SIGNATURE');

        $stripeService = StripeService::createFromConfig($this->config);

        try {
            $event = $stripeService->receiveEvent($payload, $sigHeader);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new Response(json_encode(['error' => $e->getMessage()]), 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response(json_encode(['error' => $e->getMessage()]), 400);
        }

        // file_put_contents('stripe_webhook_log.txt', $payload, FILE_APPEND);

        if ('checkout.session.completed' === $event->type) {
            $this->handleEventCheckoutSessionCompleted($event);
        }

        return new Response('', 200);
    }

    /**
     * Handles the Strip WebHook Even checkout.session.completed
     *
     * The main task of this method is to check whether the order has been paid and to set the status on the order to
     * paid.
     *
     * @link https://stripe.com/docs/api/events/types#event_types-checkout.session.completed
     *
     * @param Event $event A Strip Event
     */
    private function handleEventCheckoutSessionCompleted(Event $event): void
    {
        $newOrderStatusId = 1; // TODO: Make this configurable via the module settings

        /** @var StripeSession */
        $session           = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        $phpSessionId      = $clientReferenceId;

        if ('paid' !== $session->payment_status) {
            return;
        }

        try {
            $phpSession = $this->container->get(PhpSession::class);
            $phpSession->load($phpSessionId);
        } catch (Exception $e) {
            error_log('Can not handle stripe event checkout.session.completed - ' . $e->getMessage());
            die();
        }

        $order = $phpSession->getOrder();

        if (!$order) {
            error_log('Can not handle stripe event checkout.session.completed - order is null');
            die();
        }

        /** @var Repository */
        $repo = $this->container->get(Repository::class);
        $repo->updateOrderStatus($order->getId(), $newOrderStatusId);
        $repo->insertOrderStatusHistory($order->getId(), $newOrderStatusId);

        // Create a link between the order and the payment
        $repo->insertRthStripePayment($order->getId(), $session->payment_intent->id);
    }

    private function getSecretKey(): string
    {
        if ($this->config->getLiveMode()) {
            return $this->config->getApiLiveSecret();
        } else {
            return $this->config->getApiSandboxSecret();
        }
    }
}
