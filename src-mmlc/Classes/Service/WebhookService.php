<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\EventHandler\StripeEventHandler;
use Stripe\Event;

class WebhookService
{
    private StripeEventHandler $stripeEventHandler;
    private StripeConfig $stripeConfig;

    /**
     *  A Webhook Entpoint secret
     */
    private string $endpointSecret;

    public function __construct(
        StripeEventHandler $stripeEventHandler,
        StripeConfig $stripeConfig
    ) {
        $this->stripeEventHandler = $stripeEventHandler;
        $this->stripeConfig = $stripeConfig;

        // Globaler API Key wird für alle Stripe-Aufrufe gesetzt
        \Stripe\Stripe::setApiKey($this->stripeConfig->getActiveSecretKey());

        $this->endpointSecret = $stripeConfig->getApiLiveEndpointSecret();
    }

    /**
     * Process webhook from Stripe
     */
    public function processWebhook(string $payload, string $sigHeader): bool
    {
        $event = $this->receiveEvent($payload, $sigHeader);

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

    private function receiveEvent(string $payload, string $sigHeader): Event
    {
        // You can find your endpoint's secret in your webhook settings
        $endpointSecret = $this->endpointSecret;

        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        return $event;
    }
}
