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

/**
 * This class is intended to simplify the handling of some stripe processes. The class should help to avoid loading
 * the required configurations and conclusions from the settings yourself. It is mainly used in the controller.
 */
class StripeService
{
    private $liveMode;

    /** @var string The live or sandbox (server) secret*/
    private $secret;

    /** @var string A Webhook Entpoint secret */
    private $endpointSecret;

    /**
     * A helper method that we can use to more easily create a new StripeSerive Object.
     */
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

    public function hasValidSecret(): bool
    {
        \Stripe\Stripe::setApiKey($this->secret);
        try {
            $account = \Stripe\Account::retrieve();
            return true;
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return false;
        }
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

    /**
     * @link https://stripe.com/docs/webhooks/go-live
     */
    public function addWebhookEndpoint(string $url, array $events, string $description = '')
    {
        \Stripe\Stripe::setApiKey($this->secret);

        $endpoint = WebhookEndpoint::create([
            'url'            => $url,
            'enabled_events' => $events,
            'description'    => $description
        ]);
    }
}
