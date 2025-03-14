<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\Storage\PhpSession;
use Stripe\StripeClient;

class SessionService
{
    private const RECONSTRUCT_SESSION_TIMEOUT = 60 * 60;

    private PhpSession $phpSession;
    private PaymentRepository $paymentRepository;
    private StripeConfig $stripeConfig;

    public function __construct(
        PhpSession $phpSession,
        PaymentRepository $paymentRepository,
        StripeConfig $stripeConfig
    ) {
        $this->phpSession = $phpSession;
        $this->paymentRepository = $paymentRepository;
        $this->stripeConfig = $stripeConfig;
    }

    /**
     * Process successful Stripe checkout session
     */
    public function processSuccessfulCheckout(string $stripeSessionId): string
    {
        $stripe = new StripeClient($this->stripeConfig->getActiveSecretKey());

        $stripeCheckoutSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
        $sessionId = $stripeCheckoutSession->client_reference_id;

        $this->phpSession->load($sessionId, self::RECONSTRUCT_SESSION_TIMEOUT);
        $_SESSION['rth_stripe_status'] = 'success';

        $orderId = $this->phpSession->getOrder()->getId();
        $paymentIntentId = $stripeCheckoutSession->payment_intent;

        if ($paymentIntentId && $orderId) {
            $this->paymentRepository->add($orderId, $paymentIntentId);
        }

        return $sessionId;
    }
}
