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

namespace RobinTheHood\Stripe\Classes;

use Exception;
use RobinTheHood\ModifiedStdModule\Classes\Configuration;
use RobinTheHood\ModifiedStdModule\Classes\StdController;
use RobinTheHood\Stripe\Classes\Constants;
use RobinTheHood\Stripe\Classes\Session as PhpSession;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Event;
use Stripe\Stripe;

/**
 * The StdController can automatically forward requests to methods beginning with the invoke prefix via the ?action=
 * query parameter in the URL. If action is empty or not set, invokeIndex() is called by default.
 * The entry point of this class is in file shop-root/rth_stripe.php
 *
 * @link //TODO Documentation link to StdModule
 * @link https://github.com/RobinTheHood/modified-std-module
 */
class Controller extends StdController
{
    /**
     * @var Configuration $config
     *
     * @link https://github.com/RobinTheHood/modified-std-module#easy-access-with-class-configuration
     */
    private Configuration $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = new Configuration(Constants::MODULE_PAYMENT_NAME);
    }

    protected function invokeIndex()
    {
        die('There is nothing to do');
    }

    /**
     * This method is called after the customer clicks on the "Buy Button" on step 3 (checkout_confirmation.php)
     *
     * @see /includes/modules/payment/payment_rth_stripe.php $form_action_url
     * @link https://stripe.com/docs/checkout/quickstart
     * @link https://stripe.com/docs/payments/checkout/custom-success-page
     */
    protected function invokeCheckout(): void
    {
        require_once DIR_WS_FUNCTIONS . 'sessions.php';
        include_once DIR_WS_MODULES . 'set_session_and_cookie_parameters.php';

        $domain = HTTPS_SERVER;

        /**
         * We need to save the current PHP session, as it may have already expired if the customer takes a long time
         * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
         * placed in the shop.
         */
        $phpSession = new PhpSession();
        $sessionId  = $phpSession->save();

        $order = $phpSession->getOrder();
        if (!$order) {
            die('Can not create a Stripe session because we have no order Obj');
        }

        Stripe::setApiKey($this->config->apiSandboxSecret);
        header('Content-Type: application/json');

        $priceData = [
            'currency'     => 'eur',
            'unit_amount'  => $order->getTotal() * 100, // Betrag in Cent (20,00 €)
            'product_data' => [
                'name'        => 'Einkauf bei demo-shop.de',
                'description' => 'Bestellung von Max Mustermann am 01.01.2034',
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
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkoutSession->url);
    }

    protected function invokeSuccess(): void
    {
        require_once DIR_WS_FUNCTIONS . 'sessions.php';
        include_once DIR_WS_MODULES . 'set_session_and_cookie_parameters.php';

        $stripe = new \Stripe\StripeClient($this->config->apiSandboxSecret);

        try {
            $session      = $stripe->checkout->sessions->retrieve($_GET['session_id']);
            $phpSessionId = $session->client_reference_id;

            $phpSession = new PhpSession();
            $phpSession->load($phpSessionId);

            // TODO: Check if the order was realy paid, if possible
            // TODO: Load the php session if the payment process took too long

            // create the order
            xtc_redirect('/checkout_process.php');
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            dd('Invalid session.');
        }
    }

    public function invokeCancel(): void
    {
        dd('The order could not be paid.');

        // TODO: handle cancel
    }

    /**
     * // TODO: move this to its own Webhook Controller
     */
    protected function invokeReceiveHook(): void
    {
        //$payload = @file_get_contents('php://input');
        //file_put_contents('stripe_webhook_log.txt', $payload, FILE_APPEND);

        \Stripe\Stripe::setApiKey($this->config->apiSandboxSecret);

        // You can find your endpoint's secret in your webhook settings
        $endpointSecret = 'whsec_';

        $payload   = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event     = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // TODO: Change the status of the order (e.g. to paid)
        file_put_contents('stripe_webhook_log.txt', $payload, FILE_APPEND);
        file_put_contents('stripe_webhook_log.txt', print_r($event, true), FILE_APPEND);

        http_response_code(200);
    }

    private function handleEventCheckoutSessionCompleted(Event $event)
    {
        $newOrderStatusId = 1; // TODO: Make this configurable via the module settings

        $session           = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        $phpSessionId      = $clientReferenceId;

        if ('paid' !== $session->payment_status) {
            return;
        }

        try {
            $phpSession = new PhpSession();
            $phpSession->load($phpSessionId);
        } catch (Exception $e) {
            error_log('Can not handle stripe event checkout.session.completed - ' . $e->getMessage());
            die();
        }

        $orderId = $phpSession->getOrderId();

        if (!$orderId) {
            error_log('Can not handle stripe event checkout.session.completed - orderId is 0');
            die();
        }

        $repo = new Repository();
        $repo->updateOrderStatus($orderId, $newOrderStatusId);
        $repo->insertOrderStatusHistory($orderId, $newOrderStatusId);
    }
}
