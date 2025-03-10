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
 *
 * @phpcs:disable PSR1.Files.SideEffects
 */

$webhookEndpoint = HTTPS_SERVER . DIR_WS_CATALOG . 'rth_stripe.php?action=receiveHook';
$prefix          = 'MODULE_PAYMENT_PAYMENT_RTH_STRIPE_';

define($prefix . 'TITLE', 'Stripe Payment Module © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'A modified-shop module that enables payments via Stripe');
define($prefix . 'STATUS_TITLE', 'Enable Stripe Payment Module?');
define($prefix . 'STATUS_DESC', 'Do you want to enable payments through Stripe?');

// TEXT_TITLE (required) to display the payment name on checkout_confirmation.php, admin/customers_status.php, admin/orders.php, etc.
define($prefix . 'TEXT_TITLE', 'Stripe');

define($prefix . 'LIVE_MODE_TITLE', 'Enable Live Mode?');
define($prefix . 'LIVE_MODE_DESC', 'Should Stripe operate in Live Mode? If not, the module operates in the Sandbox Test Mode.');

/**
 * API
 */
define($prefix . 'API_SANDBOX_SECRET_TITLE', 'Secret Key in Test Mode');
define($prefix . 'API_SANDBOX_SECRET_DESC', 'Use this key to authenticate requests on your server in Test Mode. By default, you can use this key to perform any API request without restrictions. More information is included in the module installation guide.');
define($prefix . 'API_SANDBOX_KEY_TITLE', 'Publishable Key in Test Mode');
define($prefix . 'API_SANDBOX_KEY_DESC', 'Use this key for testing purposes in the client-side code of your web or mobile app. More information is included in the module installation guide.');
define($prefix . 'API_LIVE_SECRET_TITLE', 'Secret Key for Live Mode');
define($prefix . 'API_LIVE_SECRET_DESC', 'Use this key to authenticate requests on your server in Live Mode. By default, you can use this key to perform any API request without restrictions. More information is included in the module installation guide.');
define($prefix . 'API_LIVE_KEY_TITLE', 'Publishable Key in Live Mode');
define($prefix . 'API_LIVE_KEY_DESC', 'Use this key in the client-side code of your web or mobile app when you are ready to launch your app. More information is included in the module installation guide.');
define($prefix . 'API_LIVE_ENDPOINT_SECRET_TITLE', 'Secret Webhook Key');
define($prefix . 'API_LIVE_ENDPOINT_SECRET_DESC', 'This key is required for the server to verify if requests are coming from Stripe. More information is included in the module installation guide. You need the following data to set up a webhook at Stripe: <br>Webhook Endpoint: <code style="color: rgb(98, 90, 250)">' . $webhookEndpoint . '</code><br>Events: <code style="color: rgb(98, 90, 250)">checkout.session.completed</code>, <code style="color: rgb(98, 90, 250)">checkout.session.expired</code>, and <code style="color: rgb(98, 90, 250)">charge.succeeded</code>');

define($prefix . 'CHECKOUT_TITLE_TITLE', 'Checkout Title');
define($prefix . 'CHECKOUT_TITLE_DESC', 'Text to be used as the title in the Stripe Checkout.');
define($prefix . 'CHECKOUT_DESC_TITLE', 'Checkout Description');
define($prefix . 'CHECKOUT_DESC_DESC', 'Text to be used as the description in the Stripe Checkout.');

define($prefix . 'PAYMENT_TITLE_TITLE', 'Payment Name');
define($prefix . 'PAYMENT_TITLE_DESC', 'Name displayed in the checkout payment step.');
define($prefix . 'PAYMENT_DESC_TITLE', 'Payment Description');
define($prefix . 'PAYMENT_DESC_DESC', 'Description shown to customers in the checkout payment step.');

define($prefix . 'ORDER_STATUS_PENDING_TITLE', 'Order Status for Pending Payment');
define($prefix . 'ORDER_STATUS_PENDING_DESC', 'Some payment methods are recorded as paid by Stripe after a delay. What order status should the order receive in the meantime?');

define($prefix . 'ORDER_STATUS_PAID_TITLE', 'Order Status for Successful Payment');
define($prefix . 'ORDER_STATUS_PAID_DESC', 'What order status should the order receive after Stripe has verified the payment as successful?');

define($prefix . 'ORDER_STATUS_AUTHORIZED_TITLE', 'Order Status for Authorized Payment');
define($prefix . 'ORDER_STATUS_AUTHORIZED_DESC', 'What order status should the order receive when a payment has been authorized but not yet captured?');

define($prefix . 'ORDER_STATUS_CAPTURED_TITLE', 'Order Status after Capture');
define($prefix . 'ORDER_STATUS_CAPTURED_DESC', 'What order status should the order receive after an authorized payment has been successfully captured?');

define($prefix . 'ORDER_STATUS_CANCELED_TITLE', 'Order Status after Cancellation');
define($prefix . 'ORDER_STATUS_CANCELED_DESC', 'What order status should the order receive when a payment has been canceled?');

define($prefix . 'ORDER_STATUS_REFUNDED_TITLE', 'Order Status after Refund');
define($prefix . 'ORDER_STATUS_REFUNDED_DESC', 'What order status should the order receive when a payment has been refunded?');

define($prefix . 'MANUAL_CAPTURE_TITLE', 'Manual Capture');
define($prefix . 'MANUAL_CAPTURE_DESC', 'Enable manual capturing of payments? If yes, payment amounts will be authorized but not automatically captured. You must manually capture payments from the order or from your Stripe dashboard.');
