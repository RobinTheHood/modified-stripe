<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\StripeEventHandler;
use Stripe\Event;

class WebhookService
{
    private StripeEventHandler $stripeEventHandler;
    private StripeConfig $stripeConfig;

    /**
     * API secret key
     */
    private string $apiSecret;

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

        // Einmalige Initialisierung des API Keys
        $liveMode = $this->stripeConfig->getLiveMode();
        $this->apiSecret = $liveMode
            ? $this->stripeConfig->getApiLiveSecret()
            : $this->stripeConfig->getApiSandboxSecret();

        // Globaler API Key wird für alle Stripe-Aufrufe gesetzt
        \Stripe\Stripe::setApiKey($this->apiSecret);

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

    public function receiveEvent(string $payload, string $sigHeader): Event
    {
        // You can find your endpoint's secret in your webhook settings
        $endpointSecret = $this->endpointSecret;

        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        return $event;
    }
}
