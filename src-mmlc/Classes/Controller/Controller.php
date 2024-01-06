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
use RobinTheHood\Stripe\Classes\Framework\AbstractController;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\RedirectResponse;
use RobinTheHood\Stripe\Classes\Framework\Request;
use RobinTheHood\Stripe\Classes\Framework\Response;
use RobinTheHood\Stripe\Classes\Framework\SplashMessage;
use RobinTheHood\Stripe\Classes\Session as PhpSession;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\StripeEventHandler;
use RobinTheHood\Stripe\Classes\StripeService;
use RobinTheHood\Stripe\Classes\Url;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

/**
 * The AbstractController can automatically forward requests to methods beginning with the invoke prefix via the ?action=
 * query parameter in the URL. If action is empty or not set, invokeIndex() is called by default.
 * The entry point of this class is in file shop-root/rth_stripe.php
 */
class Controller extends AbstractController
{
    /**
     * Specifies the time for how long a Stripe checkout session is valid. Minimum 30 minutes, maximum 24 hours.
     */
    private const CHECKOUT_SESSION_TIMOUT = 60 * 30;

    /**
     * Specifies the time for how long the shop session should be reconstructed after a Stripe checkout session attempt.
     */
    private const RECONSTRUCT_SESSION_TIMEOUT = 60 * 60;

    private StripeConfiguration $config;

    private DIContainer $container;

    public function __construct(DIContainer $container)
    {
        parent::__construct();
        $this->config    = new StripeConfiguration('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');
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
        /**
         * We need to save the current PHP session, as it may have already expired if the customer takes a long time
         * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
         * placed in the shop.
         */
        $phpSession   = $this->container->get(PhpSession::class);
        $phpSessionId = $phpSession->save();

        $order = $phpSession->getOrder();
        if (!$order) {
            die('Can not create a Stripe session because we have no order object');
        }

        Stripe::setApiKey($this->getSecretKey());

        /**
         * //TODO: Use reasonable defaults per language.
         */
        $name        = parse_multi_language_value($this->config->checkoutTitle, $_SESSION['language_code']) ?: 'title';
        $description = parse_multi_language_value($this->config->checkoutDesc, $_SESSION['language_code']) ?: 'description';

        // Stripe only accepts values in the smallest unit (e.g. cents) without decimal places
        $priceCent = (int) round($order->getTotal() * 100);
        $currency  = strtolower($order->getCurrency());

        $priceData = [
            'currency'     => $currency, // ISO 3 letter in lower case
            'unit_amount'  => $priceCent, // Value in Cent
            'product_data' => [
                'name'        => $name,
                'description' => $description,
            ],
        ];

        /**
         * Creates a Stripe checkout session object. Don't confuse it with a PHP session. Both use the same name.
         *
         * @link https://stripe.com/docs/api/checkout/sessions/object
         */
        $checkoutSession = StripeSession::create(
            [
                'line_items'          => [
                    [
                        'price_data' => $priceData,
                        'quantity'   => 1,
                    ],
                ],
                'client_reference_id' => $phpSessionId,
                'mode'                => 'payment',
                'success_url'         => Url::create()->getStripeSuccess(),
                'cancel_url'          => Url::create()->getStripeCancel(),
                'expires_at'          => time() + (self::CHECKOUT_SESSION_TIMOUT), // Configured to expire after 30 minutes
            ]
        );

        if (!$checkoutSession->url) {
            $splashMessage = SplashMessage::getInstance(); // TODO: Move to DIContainer
            $splashMessage->error('shopping_cart', 'Can not create Stripe Checkout Session.');
            return new RedirectResponse(Url::create()->getShoppingCart());
        }

        return new RedirectResponse($checkoutSession->url);
    }

    protected function invokeSuccess(Request $request): Response
    {
        $stripe = new \Stripe\StripeClient($this->getSecretKey());

        try {
            $stripeSessionId       = $request->get('session_id');
            $stripeCheckoutSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
            $phpSessionId          = $stripeCheckoutSession->client_reference_id;

            $phpSession = $this->container->get(PhpSession::class);
            try {
                $phpSession->load($phpSessionId, self::RECONSTRUCT_SESSION_TIMEOUT);
                $_SESSION['rth_stripe_status'] = 'success';
            } catch (Exception $e) {
                $splashMessage = SplashMessage::getInstance(); // TODO: Move to DIContainer
                $splashMessage->error('shopping_cart', $e->getMessage());
                return new RedirectResponse(Url::create()->getShoppingCart());
            }

            // TODO: Check if the order was realy paid, if possible
            // TODO: Load the php session if the payment process took too long

            return new RedirectResponse(Url::create()->getCheckoutProcess());
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
        return new RedirectResponse(Url::create()->getCheckoutConfirmation());
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

        $stripeEventHandler = new StripeEventHandler($this->container);

        if ('checkout.session.completed' === $event->type) {
            $result = $stripeEventHandler->checkoutSessionCompleted($event);
            if (!$result) {
                return new Response('', 400);
            }
        }

        if ('checkout.session.expired' === $event->type) {
            $result = $stripeEventHandler->checkoutSessionExpired($event);
            if (!$result) {
                return new Response('', 400);
            }
        }

        return new Response('', 200);
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
