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
 */

use RobinTheHood\Stripe\Classes\Controller\Controller;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\RequestFactory;

/**
 * When Stripe tries to send a webhook to our script and the URL query parameter "action" is set, the
 * application_top.php (in cart_actions.php) redirects to a "cookie-must-be-enabled" page. The Stripe Webhook cannot do
 * anything with this. For this reason we use application_top_callback.php. This file doesn't do that. However, the
 * files from includes/extra/functions/ that we need for autoloading are then not loaded either. Now we have to do that
 * ourselves.
 */

require_once 'includes/application_top_callback.php';
require_once DIR_WS_FUNCTIONS . 'sessions.php';
require_once DIR_WS_MODULES . 'set_session_and_cookie_parameters.php';
require_once DIR_FS_CATALOG . 'includes/extra/functions/composer_autoload.php';
require_once DIR_FS_CATALOG . 'includes/extra/functions/rth_modified_std_module.php';
require_once DIR_WS_CLASSES . 'order_total.php';
require_once DIR_WS_CLASSES . 'order.php';
require_once DIR_WS_CLASSES . 'message_stack.php';
require_once DIR_FS_INC . 'xtc_remove_order.inc.php';

$rthDevMode = true;

if (true === $rthDevMode) {
    /** Show all error messages in the browser.  */
    restore_error_handler();
    restore_exception_handler();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL ^ E_NOTICE);

    /** Two global helper functions dd and dump that make development a little easier for us in dev mode */
    function dump(...$vars)
    {
        echo "<pre>\n";
        foreach ($vars as $var) {
            var_dump($var) . "\n";
        }
        echo "</pre>\n";
    }

    function dd(...$vars)
    {
        dump(...$vars);
        die();
    }
}

/**
 * The function rth_is_module_disabled() is part of the StdModule. It is a helper to write shorter code to check, if a
 * module is installed or not.
 *
 * @link // TODO Documentation link to StdModule
 * @link https://github.com/RobinTheHood/modified-std-module
 */
if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    die('Stripe payment modul is not active');
}

$diContainer = new DIContainer();
$request     = RequestFactory::createFromGlobals();
$controller  = new Controller($diContainer);
$response    = $controller->invoke($request);
$response->send();
