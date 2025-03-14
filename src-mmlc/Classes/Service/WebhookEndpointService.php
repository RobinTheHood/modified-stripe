<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use Exception;
use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Exception\WebhookException;
use RobinTheHood\Stripe\Classes\Routing\UrlBuilder;
use Stripe\Account;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\WebhookEndpoint;

/**
 * Manages Stripe webhook operations including creation, updates, and deletion.
 * Centralizes webhook-related functionality with proper validation.
 */
class WebhookEndpointService
{
    private const MODULE_IDENTIFIER = 'robinthehood/stripe';

    /**
     * Default webhook description
     */
    private const DEFAULT_DESCRIPTION = 'Webhook Endpoint for modified module robinthehood/stripe';

    /**
     * Standard webhook events to register
     */
    private const DEFAULT_WEBHOOK_EVENTS = [
        'checkout.session.completed',
        'checkout.session.expired',
        'charge.succeeded',
        'payment_intent.amount_capturable_updated',
    ];

    private StripeConfig $stripeConfig;
    private UrlBuilder $urlBuilder;

    public function __construct(
        StripeConfig $stripeConfig,
        UrlBuilder $urlBuilder
    ) {
        $this->stripeConfig = $stripeConfig;
        $this->urlBuilder = $urlBuilder;

        // Globaler API Key wird für alle Stripe-Aufrufe gesetzt
        \Stripe\Stripe::setApiKey($this->stripeConfig->getActiveSecretKey());
    }

    /**
     * Creates a new webhook endpoint if preconditions are met
     *
     * @return array Response with success status and message
     */
    public function connectWebhook(): array
    {
        try {
            $this->validateHasValidSecret();
            $this->validateNoWebhookExists();

            $endpoint = $this->addWebhookEndpoint(
                $this->urlBuilder->getStripeWebhook(),
                self::DEFAULT_WEBHOOK_EVENTS,
                self::DEFAULT_DESCRIPTION
            );

            $secret = $endpoint['secret'] ?? '';
            $this->stripeConfig->setWebhookSerect($secret);

            return [
                'success' => true,
                'message' => 'Stripe Webhook Endpoint erfolgreich hinzugefügt.',
            ];
        } catch (WebhookException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Updates an existing webhook endpoint if preconditions are met
     *
     * @return array Response with success status and message
     */
    public function updateWebhook(): array
    {
        try {
            $this->validateHasValidSecret();
            $this->validateWebhookExists();

            $this->updateWebhookEndpoint(
                $this->urlBuilder->getStripeWebhook(),
                self::DEFAULT_WEBHOOK_EVENTS,
                self::DEFAULT_DESCRIPTION
            );

            return [
                'success' => true,
                'message' => 'Stripe Webhook Endpoint erfolgreich aktualisiert.',
            ];
        } catch (WebhookException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Removes an existing webhook endpoint if preconditions are met
     *
     * @return array Response with success status and message
     */
    public function disconnectWebhook(): array
    {
        try {
            $this->validateHasValidSecret();
            $this->validateWebhookExists();

            $this->deleteWebhookEndpoint();
            $this->stripeConfig->setWebhookSerect('');

            return [
                'success' => true,
                'message' => 'Stripe Webhook Endpoint erfolgreich entfernt.',
            ];
        } catch (WebhookException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Checks if the configured API secret is valid
     *
     * @return bool True if the secret is valid
     */
    public function hasValidSecret(): bool
    {
        try {
            Account::retrieve();
            return true;
        } catch (AuthenticationException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Add a new webhook endpoint to Stripe
     *
     * @param string $url The webhook URL
     * @param array $events Events to subscribe to
     * @param string $description Webhook description
     * @return WebhookEndpoint The created endpoint
     */
    public function addWebhookEndpoint(string $url, array $events, string $description = ''): WebhookEndpoint
    {
        return WebhookEndpoint::create([
            'url' => $url,
            'enabled_events' => $events,
            'description' => $description,
            'metadata' => [
                'module' => self::MODULE_IDENTIFIER,
            ],
        ]);
    }

    /**
     * Updates an existing webhook endpoint
     *
     * @param string $url The webhook URL
     * @param array $events Events to subscribe to
     * @param string $description Webhook description
     */
    public function updateWebhookEndpoint(string $url, array $events, string $description = ''): void
    {
        $endpoint = $this->getWebhookEndpoint();
        if (!$endpoint) {
            throw new WebhookException('Webhook Endpoint ist nicht vorhanden.');
        }

        $settings = [
            'url' => $url,
            'enabled_events' => $events,
            'description' => $description,
        ];

        WebhookEndpoint::update($endpoint->id, $settings);
    }

    /**
     * Deletes the webhook endpoint
     */
    public function deleteWebhookEndpoint(): void
    {
        $endpoint = $this->getWebhookEndpoint();
        if (!$endpoint) {
            return;
        }

        $endpoint->delete();
    }

    /**
     * Get the current status of webhook configuration
     *
     * @return int 0 = no webhook, 1 = webhook needs update, 2 = webhook correctly configured
     */
    public function getWebhookStatus(): int
    {
        return $this->checkWebhookEndpointStatus(
            $this->urlBuilder->getStripeWebhook(),
            self::DEFAULT_WEBHOOK_EVENTS
        );
    }

    /**
     * Get the endpoint of this module
     *
     * @return WebhookEndpoint|null Webhook endpoint if found
     */
    public function getWebhookEndpoint(): ?WebhookEndpoint
    {
        try {
            $endpoints = WebhookEndpoint::all();

            if (empty($endpoints['data'])) {
                return null;
            }

            foreach ($endpoints['data'] as $endpoint) {
                if ($this->isModuleEndpoint($endpoint)) {
                    return $endpoint;
                }
            }

            return null;
        } catch (ApiErrorException $e) {
            return null;
        }
    }

    /**
     * Check if a webhook endpoint is created by this module
     *
     * @return bool True if webhook exists
     */
    public function hasWebhookEndpoint(): bool
    {
        return $this->getWebhookEndpoint() !== null;
    }

    /**
     * Check webhook endpoint status
     *
     * @param string $url The expected webhook URL
     * @param array $events The expected events
     * @return int 0 = no webhook, 1 = webhook needs update, 2 = webhook correctly configured
     */
    public function checkWebhookEndpointStatus(string $url, array $events): int
    {
        try {
            $endpoint = $this->getWebhookEndpoint();

            if (!$endpoint) {
                return 0; // No webhook exists
            }

            if ($endpoint->url !== $url) {
                return 1; // URL mismatch, needs update
            }

            sort($events);
            $endpointEvents = $endpoint->enabled_events;
            sort($endpointEvents);

            if ($events !== $endpointEvents) {
                return 1; // Events mismatch, needs update
            }

            return 2; // Correctly configured
        } catch (Exception $e) {
            return 0; // Error or no webhook
        }
    }

    /**
     * Validates that a valid API secret is available
     * @throws WebhookException
     */
    private function validateHasValidSecret(): void
    {
        if (!$this->hasValidSecret()) {
            throw new WebhookException('Kein valider Live- oder Test-Modus API Secret vorhanden.');
        }
    }

    /**
     * Validates that no webhook endpoint exists yet
     * @throws WebhookException
     */
    private function validateNoWebhookExists(): void
    {
        if ($this->hasWebhookEndpoint()) {
            throw new WebhookException('Webhook Endpoint ist bereits vorhanden.');
        }
    }

    /**
     * Validates that a webhook endpoint already exists
     * @throws WebhookException
     */
    private function validateWebhookExists(): void
    {
        if (!$this->hasWebhookEndpoint()) {
            throw new WebhookException('Webhook Endpoint ist nicht vorhanden.');
        }
    }

    /**
     * Check if the endpoint is created by this module
     *
     * @param WebhookEndpoint $endpoint
     * @return bool
     */
    private function isModuleEndpoint(WebhookEndpoint $endpoint): bool
    {
        $metadata = $endpoint['metadata'] ?? [];
        $module = $metadata['module'] ?? '';

        return self::MODULE_IDENTIFIER === $module;
    }
}
