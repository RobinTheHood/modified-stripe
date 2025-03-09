<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\StripeEventHandler;
use RobinTheHood\Stripe\Classes\StripeService;
use Stripe\Stripe;

class WebhookService
{
    private StripeEventHandler $stripeEventHandler;
    private StripeConfiguration $config;

    public function __construct(
        StripeEventHandler $stripeEventHandler,
        StripeConfiguration $config
    ) {
        $this->stripeEventHandler = $stripeEventHandler;
        $this->config = $config;
    }

    /**
     * Process webhook from Stripe
     */
    public function processWebhook(string $payload, string $sigHeader): bool
    {
        $stripeService = StripeService::createFromConfig($this->config);
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
