<?php

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes;

use Exception;
use Stripe\Event;
use Stripe\WebhookEndpoint;

class StripeService
{
    private $liveMode;

    private $secret;

    private $endpointSecret;

    public static function createFromConfig(StripeConfiguration $config): StripeService
    {
        $liveMode = $config->getLiveMode();

        if ($liveMode) {
            $secret = $config->getApiLiveSecret();
        } else {
            $secret = $config->getApiSandboxSecret();
        }

        $endpointSecret = $config->getApiLiveEndpointSecret();

        return new StripeService(
            $liveMode,
            $secret,
            $endpointSecret
        );
    }

    public function __construct(bool $liveMode, string $secret, string $endpointSecret)
    {
        $this->liveMode       = $liveMode;
        $this->secret         = $secret;
        $this->endpointSecret = $endpointSecret;
    }

    public function receiveEvent(string $payload, string $sigHeader): Event
    {
        \Stripe\Stripe::setApiKey($this->secret);

        // You can find your endpoint's secret in your webhook settings
        $endpointSecret = $this->endpointSecret;

        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        return $event;
    }

    public function hasWebhookEndpoint(): bool
    {
        try {
            \Stripe\Stripe::setApiKey($this->secret);
            $endpoints = WebhookEndpoint::all();
        } catch (Exception $e) {
            return false;
        }

        if (!$endpoints['data']) {
            return false;
        }

        return true;
    }
}
