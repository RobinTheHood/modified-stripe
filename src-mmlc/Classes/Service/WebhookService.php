<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\StripeEventHandler;
use RobinTheHood\Stripe\Classes\StripeService;

class WebhookService
{
    /** @var DIContainer */
    private $container;

    /** @var StripeConfiguration */
    private $config;

    public function __construct(
        DIContainer $container,
        StripeConfiguration $config
    ) {
        $this->container = $container;
        $this->config = $config;
    }


    /**
     * Process webhook from Stripe
     */
    public function processWebhook(string $payload, string $sigHeader): bool
    {
        $stripeService = StripeService::createFromConfig($this->config);
        $event = $stripeService->receiveEvent($payload, $sigHeader);

        $stripeEventHandler = new StripeEventHandler($this->container);

        switch ($event->type) {
            case 'checkout.session.completed':
                return $stripeEventHandler->checkoutSessionCompleted($event);
            case 'checkout.session.expired':
                return $stripeEventHandler->checkoutSessionExpired($event);
            case 'payment_intent.amount_capturable_updated':
                return $stripeEventHandler->paymentIntentAmountCapturableUpdated($event);
            default:
                return true;
        }
    }
}
