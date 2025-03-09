<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use Exception;
use RobinTheHood\Stripe\Classes\Session;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\Url;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class CheckoutService
{
    private const CHECKOUT_SESSION_TIMOUT = 60 * 30;

    private Session $phpSession;
    private $config;

    public function __construct(
        Session $phpSession,
        StripeConfiguration $config
    ) {
        $this->phpSession = $phpSession;
        $this->config = $config;
    }

    /**
     * Creates a Stripe checkout session
     */
    public function createCheckoutSession(): StripeSession
    {
        $phpSessionId = $this->phpSession->save();

        $order = $this->phpSession->getOrder();
        if (!$order) {
            throw new Exception('Can not create a Stripe session because we have no order object');
        }

        Stripe::setApiKey($this->getSecretKey());

        $lineItems = $this->createLineItems($order);

        $sessionParams = [
            'line_items' => $lineItems,
            'client_reference_id' => $phpSessionId,
            'mode' => 'payment',
            'success_url' => Url::create()->getStripeSuccess(),
            'cancel_url' => Url::create()->getStripeCancel(),
            'expires_at' => time() + self::CHECKOUT_SESSION_TIMOUT,
        ];

        // Only add payment_intent_data with manual capture if the setting is enabled
        if ($this->config->getManualCapture()) {
            $sessionParams['payment_intent_data'] = [
                'capture_method' => 'manual',
            ];
        }

        // Add metadata to the session payment_intent
        $sessionParams['payment_intent_data']['metadata'] = [
            'order_id' => $order->getId(),
            'php_session_id' => $phpSessionId,
            'customer_email' => $order->getCustomerEmail(),
        ];

        return StripeSession::create($sessionParams);
    }

    /**
     * Creates line items for the Stripe checkout session
     */
    private function createLineItems($order): array
    {
        $name = parse_multi_language_value(
            $this->config->checkoutTitle,
            $_SESSION['language_code']
        ) ?: 'title';

        $description = parse_multi_language_value(
            $this->config->checkoutDesc,
            $_SESSION['language_code']
        ) ?: 'description';

        // Stripe only accepts values in the smallest unit (e.g. cents) without decimal places
        $priceCent = (int) round($order->getTotal() * 100);
        $currency = strtolower($order->getCurrency());

        return [
            [
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $priceCent,
                    'product_data' => [
                        'name' => $name,
                        'description' => $description,
                    ],
                ],
                'quantity' => 1,
            ],
        ];
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
