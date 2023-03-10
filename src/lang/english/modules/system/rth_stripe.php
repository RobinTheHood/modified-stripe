<?php

$moduleType = 'MODULE';
$moduleName = 'RTH_STRIPE';
$prefix     = $moduleType . '_' . $moduleName  . '_';

define($prefix . 'TITLE', 'Stripe Paymentmodule © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'A modified-shop module that allows payments via Stripe.');
define($prefix . 'STATUS_TITLE', 'robinthehood/stripe Modul active?');
define($prefix . 'STATUS_DESC', '');

/**
 * API
 */
define($prefix . 'API_SANDBOX_KEY_TITLE', 'Test mode secret key');
define($prefix . 'API_SANDBOX_KEY_DESC', 'Use this key to authenticate requests on your server when in test mode. By default, you can use this key to perform any API request without restriction.');
define($prefix . 'API_SANDBOX_SECRET_TITLE', 'Test mode publishable key');
define($prefix . 'API_SANDBOX_SECRET_DESC', 'Use this key for testing purposes in your web or mobile app\'s client-side code.');

define($prefix . 'API_LIVE_KEY_TITLE', 'Live mode secret key');
define($prefix . 'API_LIVE_KEY_DESC', 'Use this key to authenticate requests on your server when in live mode. By default, you can use this key to perform any API request without restriction.');
define($prefix . 'API_LIVE_SECRET_TITLE', 'Live mode publishable key');
define($prefix . 'API_LIVE_SECRET_DESC', 'Use this key, when you’re ready to launch your app, in your web or mobile app’s client-side code.');
/** */
