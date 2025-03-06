<?php

namespace RobinTheHood\Stripe\Classes\View;

use Stripe\PaymentIntent;
use Stripe\Charge;

class OrderDetailView
{
    private PaymentIntent $paymentIntent;
    private ?Charge $charge = null;

    public function __construct(PaymentIntent $paymentIntent, ?Charge $charge = null)
    {
        $this->paymentIntent = $paymentIntent;
        $this->charge = $charge;
    }

    public function getPaymentIntent(): PaymentIntent
    {
        return $this->paymentIntent;
    }

    public function getCharge(): ?Charge
    {
        return $this->charge;
    }

    /**
     * Formats a timestamp to a human-readable date and time
     */
    public function formatTimestamp($timestamp): string
    {
        if (empty($timestamp)) {
            return '';
        }
        return date('d.m.Y H:i:s', $timestamp);
    }

    /**
     * Formats a monetary amount with appropriate currency symbol
     */
    public function formatAmount($amount, $currency): string
    {
        $amount = $amount / 100; // Convert from cents
        $currency = strtoupper($currency); // Ensure currency is uppercase
        $currencies = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
        ];
        $symbol = isset($currencies[$currency]) ? $currencies[$currency] : $currency;
        return number_format($amount, 2, ',', '.') . ' ' . $symbol;
    }

    /**
     * Gets the appropriate CSS class for a payment status badge
     */
    public function getStatusBadgeClass($status): string
    {
        switch ($status) {
            case 'succeeded':
                return 'success';
            case 'processing':
                return 'warning';
            case 'requires_payment_method':
            case 'requires_confirmation':
            case 'requires_action':
                return 'info';
            case 'canceled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Calculates the capture deadline for a payment intent
     */
    public function calculateCaptureDeadline(): int
    {
        // First check if we have a charge object with capture_before field
        if (
            $this->charge && isset($this->charge->payment_method_details) &&
            isset($this->charge->payment_method_details->card) &&
            isset($this->charge->payment_method_details->card->capture_before)
        ) {
            return $this->charge->payment_method_details->card->capture_before;
        }

        // Fall back to our 7-day calculation if the field isn't available
        return $this->paymentIntent->created + (7 * 24 * 60 * 60); // 7 days in seconds
    }

    /**
     * Calculates and formats the remaining time until a deadline
     */
    public function getRemainingTimeText($deadlineTimestamp): string
    {
        $now = time();
        $remainingSeconds = $deadlineTimestamp - $now;

        if ($remainingSeconds <= 0) {
            return 'Abgelaufen';
        }

        $days = floor($remainingSeconds / (24 * 60 * 60));
        $hours = floor(($remainingSeconds % (24 * 60 * 60)) / (60 * 60));

        if ($days > 0) {
            return $days . ' Tag' . ($days > 1 ? 'e' : '') . ' und ' . $hours . ' Stunde' . ($hours > 1 ? 'n' : '');
        }

        return $hours . ' Stunde' . ($hours > 1 ? 'n' : '');
    }
}
