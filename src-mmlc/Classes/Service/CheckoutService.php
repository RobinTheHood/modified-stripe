<?php

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use Exception;
use RobinTheHood\Stripe\Classes\Session;
use RobinTheHood\Stripe\Classes\Url;
use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class CheckoutService
{
    private const CHECKOUT_SESSION_TIMOUT = 60 * 30;

    private Session $phpSession;
    private StripeConfig $stripeConfig;

    public function __construct(
        Session $phpSession,
        StripeConfig $stripeConfig,
    ) {
        $this->phpSession = $phpSession;
        $this->stripeConfig = $stripeConfig;
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
            'success_url' => $this->urlBuilder->getStripeSuccess(),
            'cancel_url' => $this->urlBuilder->getStripeCancel(),
            'expires_at' => time() + self::CHECKOUT_SESSION_TIMOUT,
        ];

        // Only add payment_intent_data with manual capture if the setting is enabled
        if ($this->stripeConfig->getManualCapture()) {
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
            $this->stripeConfig->checkoutTitle,
            $_SESSION['language_code']
        ) ?: 'title';

        $description = parse_multi_language_value(
            $this->stripeConfig->checkoutDesc,
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
        if ($this->stripeConfig->getLiveMode()) {
            return $this->stripeConfig->getApiLiveSecret();
        } else {
            return $this->stripeConfig->getApiSandboxSecret();
        }
    }
}
