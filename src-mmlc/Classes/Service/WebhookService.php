<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\StripeEventHandler;
use RobinTheHood\Stripe\Classes\StripeService;

class WebhookService
{
    private StripeEventHandler $stripeEventHandler;
    private StripeConfig $stripeConfig;

    public function __construct(
        StripeEventHandler $stripeEventHandler,
        StripeConfig $stripeConfig
    ) {
        $this->stripeEventHandler = $stripeEventHandler;
        $this->stripeConfig = $stripeConfig;
    }

    /**
     * Process webhook from Stripe
     */
    public function processWebhook(string $payload, string $sigHeader): bool
    {
        $stripeService = StripeService::createFromConfig($this->stripeConfig);
        $event = $stripeService->receiveEvent($payload, $sigHeader);

        switch ($event->type) {
            case 'checkout.session.completed':
                return $this->stripeEventHandler->checkoutSessionCompleted($event);
            case 'checkout.session.expired':
                return $this->stripeEventHandler->checkoutSessionExpired($event);
            case 'payment_intent.amount_capturable_updated':
                return $this->stripeEventHandler->paymentIntentAmountCapturableUpdated($event);
            default:
                return true;
        }
    }
}
