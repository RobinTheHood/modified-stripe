<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Session as PhpSession;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use Stripe\StripeClient;

class SessionService
{
    private const RECONSTRUCT_SESSION_TIMEOUT = 60 * 60;

    private DIContainer $container;
    private StripeConfiguration $config;

    public function __construct(
        DIContainer $container,
        StripeConfiguration $config
    ) {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * Process successful Stripe checkout session
     */
    public function processSuccessfulCheckout(string $stripeSessionId): string
    {
        $stripe = new StripeClient($this->getSecretKey());
        $stripeCheckoutSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
        $phpSessionId = $stripeCheckoutSession->client_reference_id;

        $phpSession = $this->container->get(PhpSession::class);
        $phpSession->load($phpSessionId, self::RECONSTRUCT_SESSION_TIMEOUT);
        $_SESSION['rth_stripe_status'] = 'success';

        return $phpSessionId;
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
