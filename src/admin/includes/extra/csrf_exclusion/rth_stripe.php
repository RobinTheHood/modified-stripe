<?php

/**
 * The function rth_is_module_disabled() is part of the StdModule. It is a helper to write shorter code to check, if a
 * module is installed or not.
 *
 * @link https://github.com/RobinTheHood/modified-std-module
 */

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

$module_exclusions[] = 'rth_stripe';
