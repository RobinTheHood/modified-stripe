<?php

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

namespace RobinTheHood\Stripe;

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

// Only load on orders.php page
if (basename($_SERVER['PHP_SELF']) !== 'orders.php') {
    return;
}

?>

<style>
.stripe-temp-order-indicator {
    display: inline-block;
    background-color: #635bff;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    margin-left: 5px;
    vertical-align: middle;
}

.stripe-temp-order-icon {
    margin-right: 3px;
}

.stripe-temp-order-indicator:hover {
    background-color: #5a54d9;
}
</style>
