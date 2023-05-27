<?php

use Stripe\StripeClient;
use RobinTheHood\Stripe\Classes\Constants;

include 'includes/application_top.php';

if (rth_is_module_disabled(Constants::MODULE_SYSTEM_NAME)) {
    return;
}

$stripe = new StripeClient('sk_test_BQokikJOvBiI2HlWgH4olfQ2');

$customer = $stripe->customers->create([
    'description'    => 'example customer',
    'email'          => 'email@example.com',
    'payment_method' => 'pm_card_visa',
]);

echo $customer;
