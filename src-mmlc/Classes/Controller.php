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

namespace RobinTheHood\Stripe\Classes;

use RobinTheHood\ModifiedStdModule\Classes\Configuration;
use RobinTheHood\ModifiedStdModule\Classes\StdController;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use order as Order; // modified class order. We do this, because Order with a capital O looks nicer than order with small o

/**
 * The StdController can automatically forward requests to methods beginning with the invoke prefix via the ?action=
 * parameter in the URL. If action is empty or not set, invokeIndex() is called by default.
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
        $this->config = new Configuration('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');
    }

    protected function invokeIndex()
    {
        die('There is nothing to do');
    }

    /**
     * @link https://stripe.com/docs/checkout/quickstart
     */
    protected function invokeCheckout(): void
    {
        Stripe::setApiKey($this->config->apiSandboxSecret);
        header('Content-Type: application/json');

        $domain = HTTPS_SERVER;

        /**
         * We need to save the current PHP session, as it may have already expired if the customer takes a long time
         * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
         * placed in the shop.
         */
        $order = $this->getOrder();
        $sessionId = $this->createHashFromOrder($order);
        $this->savePhpSession($sessionId);

        $checkoutSession = Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1NBDB1JIsfvAtVBddfc2gRn6',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $domain . '/success.html',
            'cancel_url' => $domain . '/cancel.html',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkoutSession->url);
    }

    private function savePhpSession(string $sessionId): void
    {
        $sessionData = serialize($_SESSION);
        // TODO ...
    }

    private function loadPhpSession(string $sessionId)
    {
        $_SESSION = unserialize($sessionData);
        // TODO ...
    }

    private function getOrder(): Order
    {
        global $order;
        return $order;
    }

    private function createHashFromOrder(Order $order): string
    {
        return '';
    }
}
