<?php

use Stripe\StripeClient;

include 'includes/application_top.php';

if (rth_is_module_disabled('MODULE_RTH_STRIPE')) {
    return;
}

$stripe = new StripeClient('sk_test_BQokikJOvBiI2HlWgH4olfQ2');
$customer = $stripe->customers->create([
    'description' => 'example customer',
    'email' => 'email@example.com',
    'payment_method' => 'pm_card_visa',
]);

echo $customer;
