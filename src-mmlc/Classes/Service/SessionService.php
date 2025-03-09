<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Storage\PhpSession;
use Stripe\StripeClient;

class SessionService
{
    private const RECONSTRUCT_SESSION_TIMEOUT = 60 * 60;

    private PhpSession $phpSession;
    private StripeConfig $stripeConfig;

    public function __construct(
        PhpSession $phpSession,
        StripeConfig $stripeConfig
    ) {
        $this->phpSession = $phpSession;
        $this->stripeConfig = $stripeConfig;
    }

    /**
     * Process successful Stripe checkout session
     */
    public function processSuccessfulCheckout(string $stripeSessionId): string
    {
        $stripe = new StripeClient($this->getSecretKey());
        $stripeCheckoutSession = $stripe->checkout->sessions->retrieve($stripeSessionId);
        $sessionId = $stripeCheckoutSession->client_reference_id;

        $this->phpSession->load($sessionId, self::RECONSTRUCT_SESSION_TIMEOUT);
        $_SESSION['rth_stripe_status'] = 'success';

        return $sessionId;
    }

    /**
     * Get the correct secret key based on mode
     */
    private function getSecretKey(): string
    {
        if ($this->stripeConfig->getLiveMode()) {
            return $this->stripeConfig->getApiLiveSecret();
        } else {
            return $this->stripeConfig->getApiSandboxSecret();
        }
    }
}
