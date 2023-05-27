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

use RobinTheHood\Stripe\Classes\{Controller, Constants};

include 'includes/application_top_callback.php';
require_once DIR_FS_CATALOG . 'includes/extra/functions/composer_autoload.php';
require_once DIR_FS_CATALOG . 'includes/extra/functions/rth_modified_std_module.php';

$rthDevMode = true;

if (true === $rthDevMode) {
    restore_error_handler();
    restore_exception_handler();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL ^ E_NOTICE);
}

/**
 * The function rth_is_module_disabled() is part of the StdModule. It is a helper to wrtie shorter code to check, if a
 * module is installed or not.
 *
 * @link //TODO Documentation link to StdModule
 * @link https://github.com/RobinTheHood/modified-std-module
 */
if (rth_is_module_disabled(Constants::MODULE_PAYMENT_NAME)) {
    die('Stripe payment modul is not active');
}

$controller = new Controller();
$controller->invoke();
