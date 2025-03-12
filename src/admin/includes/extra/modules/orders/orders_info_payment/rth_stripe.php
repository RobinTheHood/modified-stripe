<?php

use RobinTheHood\Stripe\Classes\Controller\AdminController;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

if (payment_rth_stripe::class !== $order->info['payment_method']) {
    return;
}

// Get the order ID from the URL parameter
$orderId = isset($_GET['oID']) ? (int)$_GET['oID'] : 0;

$diContainer = new DIContainer();
// $repo = $diContainer->get(Repository::class);
// $stripePaymentIntent = $repo->getStripePaymentByOrderId($orderId);

$paymentRepo = $diContainer->get(PaymentRepository::class);
$paymentInent = $paymentRepo->findByOrderId($orderId);
$stripePaymentIntentId = $paymentInent['stripe_payment_intent_id'] ?? null;

include AdminController::TEMPLATE_PATH . 'OrderDetail.tmpl.php';
