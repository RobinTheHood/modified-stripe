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

use RobinTheHood\ModifiedStdModule\Classes\Configuration;
use RobinTheHood\ModifiedStdModule\Classes\StdController;
use RobinTheHood\Stripe\Classes\Constants;
use RobinTheHood\Stripe\Classes\Session as PhpSession;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

/**
 * The StdController can automatically forward requests to methods beginning with the invoke prefix via the ?action=
 * parameter in the URL. If action is empty or not set, invokeIndex() is called by default.
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
        $this->config = new Configuration(Constants::MODULE_NAME);
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
     */
    protected function invokeCheckout(): void
    {
        /**
         * We need to save the current PHP session, as it may have already expired if the customer takes a long time
         * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
         * placed in the shop.
         */
        $phpSession = new PhpSession();
        $sessionId = $phpSession->save();

        $order = $phpSession->getOrder();
        if (!$order) {
            die('Can not create a Stripe session because we have no order Obj');
        }

        // var_dump($order);
        // die();

        Stripe::setApiKey($this->config->apiSandboxSecret);
        header('Content-Type: application/json');

        $domain = HTTPS_SERVER;


        $priceData = array(
            'currency' => 'eur',
            'unit_amount' => $order->getTotal() * 100, // Betrag in Cent (20,00 €)
            'product_data' => array(
                'name' => 'Einkauf bei demo-shop.de',
                'description' => 'Bestellung von Max Mustermann am 01.01.2034'
            )
        );

        /**
         * Create is a Stripe checkout session object. Don't confuse it with a PHP session. Both use the same name.
         *
         * @link https://stripe.com/docs/api/checkout/sessions/object
         */
        $checkoutSession = StripeSession::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                //'price' => 'price_1NBDB1JIsfvAtVBddfc2gRn6',
                'price_data' => $priceData,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $domain . '/success.html',
            'cancel_url' => $domain . '/cancel.html',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkoutSession->url);
    }

    private function createHashFromOrder(Order $order): string
    {
        return '';
    }
}
