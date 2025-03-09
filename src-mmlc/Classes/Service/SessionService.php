<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Session;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use Stripe\StripeClient;

class SessionService
{
    private const RECONSTRUCT_SESSION_TIMEOUT = 60 * 60;

    private Session $session;
    private StripeConfiguration $config;

    public function __construct(
        Session $session,
        StripeConfiguration $config
    ) {
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Process successful Stripe checkout session
     */
    public function processSuccessfulCheckout(string $stripeSessionId): string
    {
        $stripe = new StripeClient($this->getSecretKey());
        $stripeCheckoutSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
        $sessionId = $stripeCheckoutSession->client_reference_id;

        $this->session->load($sessionId, self::RECONSTRUCT_SESSION_TIMEOUT);
        $_SESSION['rth_stripe_status'] = 'success';

        return $sessionId;
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
