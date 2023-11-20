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

$prefix = 'MODULE_PAYMENT_PAYMENT_RTH_STRIPE_';

define($prefix . 'TITLE', 'Stripe Paymentmodule © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'A modified-shop module that allows payments via Stripe.');
define($prefix . 'STATUS_TITLE', 'robinthehood/stripe Modul active?');
define($prefix . 'STATUS_DESC', '');

// TEXT_TITLE (required) to display the payment name on checkout_confirmation.php, admin/customers_status.php, admin/orders.php etc.
define($prefix . 'TEXT_TITLE', 'Stripe');

/**
 * API
 */
define($prefix . 'API_SANDBOX_SECRET_KEY_TITLE', 'Test mode secret key');
define($prefix . 'API_SANDBOX_SECRET_KEY_DESC', 'Use this key to authenticate requests on your server when in test mode. By default, you can use this key to perform any API request without restriction.');
define($prefix . 'API_SANDBOX_KEY_TITLE', 'Test mode publishable key');
define($prefix . 'API_SANDBOX_KEY_DESC', 'Use this key for testing purposes in your web or mobile app\'s client-side code.');
define($prefix . 'API_LIVE_SECRET_KEY_TITLE', 'Live mode secret key');
define($prefix . 'API_LIVE_SECRET_KEY_DESC', 'Use this key to authenticate requests on your server when in live mode. By default, you can use this key to perform any API request without restriction.');
define($prefix . 'API_LIVE_KEY_TITLE', 'Live mode publishable key');
define($prefix . 'API_LIVE_KEY_DESC', 'Use this key, when you’re ready to launch your app, in your web or mobile app’s client-side code.');
define($prefix . 'API_LIVE_ENDPOINT_SECRET_TITLE', 'Secret Webhook Key');
define($prefix . 'API_LIVE_ENDPOINT_SECRET_DESC', 'This key is needed so that the server can check whether the requests come from Stripe.');

define($prefix . 'CHECKOUT_TITLE_TITLE', 'Checkout title');
define($prefix . 'CHECKOUT_TITLE_DESC', 'Text to be used as the title in the Stripe Checkout.');
define($prefix . 'CHECKOUT_DESC_TITLE', 'Checkout description');
define($prefix . 'CHECKOUT_DESC_DESC', 'Text to be used as description in the Stripe Checkout.');
