<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentCaptureService
{
    private PaymentRepository $paymentRepo;
    private StripeConfiguration $config;

    public function __construct(
        PaymentRepository $paymentRepo,
        StripeConfiguration $config
    ) {
        $this->paymentRepo = $paymentRepo;
        $this->config = $config;
    }

    /**
     * Capture a payment for an order
     */
    public function capturePayment(int $orderId): void
    {
        $paymentData = $this->paymentRepo->findByOrderId($orderId);

        if (!$paymentData || empty($paymentData['stripe_payment_intent_id'])) {
            throw new \Exception('Keine Stripe Zahlungsdaten für diese Bestellung gefunden.');
        }

        $paymentIntentId = $paymentData['stripe_payment_intent_id'];

        Stripe::setApiKey($this->getSecretKey());

        // Retrieve the payment intent from Stripe
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

        // Capture the payment
        $paymentIntent->capture();
    }

    /**
     * Get the correct secret key based on mode
     */
    private function getSecretKey(): string
    {
        if ($this->config->getLiveMode()) {
            return $this->config->getApiLiveSecret();
        } else {
            return $this->config->getApiSandboxSecret();
        }
    }
}
