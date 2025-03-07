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

use RobinTheHood\Stripe\Classes\Controller\AdminController;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\RequestFactory;

$rthGet = $_GET;
$rthPost = $_POST;
$rthServer = $_SERVER;

require_once 'includes/application_top.php';

$_GET = $rthGet;
$_POST = $rthPost;
$_SERVER = $rthServer;

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

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    die('Stripe payment modul is not active');
}

$diContainer = new DIContainer();
$request = RequestFactory::createFromGlobals();
$controller = new AdminController($diContainer);
$response = $controller->invoke($request);
$response->send();
