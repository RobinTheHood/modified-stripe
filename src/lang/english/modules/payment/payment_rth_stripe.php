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

use RobinTheHood\Stripe\Classes\Constants;

$prefix = Constants::MODULE_PAYMENT_NAME . '_';

define($prefix . 'TITLE', 'Stripe Paymentmodule © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'A modified-shop module that allows payments via Stripe.');
define($prefix . 'STATUS_TITLE', 'robinthehood/stripe Modul active?');
define($prefix . 'STATUS_DESC', '');

// TEXT_TITLE (required) to display the payment name on checkout_confirmation.php, admin/customers_status.php, etc.
define($prefix . 'TEXT_TITLE', 'Stripe (RobinTheHood)');

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
/** */

/**
 * Checkout
 */
define($prefix . 'CHECKOUT_TITLE_TITLE', 'Checkout title');
define($prefix . 'CHECKOUT_TITLE_DESC', 'Text that appears at the top of the Stripe checkout (such as <i>shopping at demo-shop.co.uk</i>).');
define($prefix . 'CHECKOUT_DESC_TITLE', 'Checkout description');
define($prefix . 'CHECKOUT_DESC_DESC', 'Text that appears at the bottom of the Stripe checkout (e.g. <i>Order from Max Mustermann on 01.01.2034</i>)');
/** */
