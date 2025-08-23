<?php

/**
 * Stripe integration for modified
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Framework\Order;
use RobinTheHood\Stripe\Classes\Repository\OrderRepository;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\View\OrderDetailView;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CaptureReminderService
{
    private PaymentRepository $paymentRepo;
    private OrderRepository $orderRepo;
    private StripeConfig $stripeConfig;

    public function __construct(
        PaymentRepository $paymentRepo,
        OrderRepository $orderRepo,
        StripeConfig $stripeConfig
    ) {
        $this->paymentRepo = $paymentRepo;
        $this->orderRepo = $orderRepo;
        $this->stripeConfig = $stripeConfig;
    }

    /**
     * Check for payments nearing their capture deadline and send reminder emails
     * 
     * @return int Number of reminders sent
     */
    public function checkAndSendReminders(): int
    {
        Stripe::setApiKey($this->stripeConfig->getActiveSecretKey());
        
        $payments = $this->paymentRepo->findPaymentsWithoutReminders();
        $remindersSent = 0;

        foreach ($payments as $payment) {
            if ($this->shouldSendReminder($payment)) {
                $this->sendReminderEmail($payment);
                $this->paymentRepo->markReminderSent((int)$payment['id']);
                $remindersSent++;
            }
        }

        return $remindersSent;
    }

    /**
     * Check if a reminder should be sent for a payment
     * 
     * @param array $payment
     * @return bool
     */
    private function shouldSendReminder(array $payment): bool
    {
        try {
            // Get the PaymentIntent from Stripe
            $paymentIntent = PaymentIntent::retrieve($payment['stripe_payment_intent_id']);
            
            // Only send reminders for payments that require capture
            if ($paymentIntent->capture_method !== 'manual') {
                return false;
            }

            // Only send reminders for payments that can still be captured
            if ($paymentIntent->status !== 'requires_capture') {
                return false;
            }

            // Calculate capture deadline using the same logic as OrderDetailView
            $orderDetailView = new OrderDetailView($paymentIntent);
            $captureDeadline = $orderDetailView->calculateCaptureDeadline();
            
            // Check if we're within 24 hours of the deadline
            $now = time();
            $hoursUntilDeadline = ($captureDeadline - $now) / 3600;
            
            // Send reminder if between 1 and 24 hours remain
            return $hoursUntilDeadline > 1 && $hoursUntilDeadline <= 24;
        } catch (\Exception $e) {
            // If we can't check the payment intent, don't send reminder
            return false;
        }
    }

    /**
     * Send reminder email for a payment
     * 
     * @param array $payment
     * @return void
     */
    private function sendReminderEmail(array $payment): void
    {
        try {
            $orderId = (int)$payment['order_id'];
            $modifiedOrder = $this->orderRepo->findByOrderId($orderId);
            
            if (!$modifiedOrder) {
                return;
            }

            $order = new Order($orderId, $modifiedOrder);
            $paymentIntent = PaymentIntent::retrieve($payment['stripe_payment_intent_id']);
            $orderDetailView = new OrderDetailView($paymentIntent);
            
            $captureDeadline = $orderDetailView->calculateCaptureDeadline();
            $deadlineFormatted = $orderDetailView->formatTimestamp($captureDeadline);
            $remainingTime = $orderDetailView->getRemainingTimeText($captureDeadline);
            
            $subject = 'Stripe Capture Deadline Reminder - Order #' . $orderId;
            $message = $this->buildEmailMessage($order, $paymentIntent, $deadlineFormatted, $remainingTime);
            
            // Use PHP's mail function - in a real implementation, you might want to use
            // the existing email system from the modified e-commerce platform
            $merchantEmail = $this->getMerchantEmail();
            $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            mail($merchantEmail, $subject, $message, $headers);
        } catch (\Exception $e) {
            // Log the error but don't throw - we don't want to stop processing other reminders
            error_log('Capture reminder email failed: ' . $e->getMessage());
        }
    }

    /**
     * Build the email message content
     * 
     * @param Order $order
     * @param PaymentIntent $paymentIntent
     * @param string $deadlineFormatted
     * @param string $remainingTime
     * @return string
     */
    private function buildEmailMessage(Order $order, PaymentIntent $paymentIntent, string $deadlineFormatted, string $remainingTime): string
    {
        $orderId = $order->getModifiedOrderId();
        $customerEmail = $order->getCustomerEmail();
        $amount = number_format($paymentIntent->amount / 100, 2);
        $currency = strtoupper($paymentIntent->currency);
        
        return "
        <html>
        <head>
            <title>Stripe Capture Deadline Reminder</title>
        </head>
        <body>
            <h2>Stripe Payment Capture Reminder</h2>
            <p>This is a reminder that a Stripe payment is approaching its capture deadline.</p>
            
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order ID:</strong> #{$orderId}</li>
                <li><strong>Customer Email:</strong> {$customerEmail}</li>
                <li><strong>Amount:</strong> {$amount} {$currency}</li>
                <li><strong>Payment Intent ID:</strong> {$paymentIntent->id}</li>
            </ul>
            
            <h3>Capture Information:</h3>
            <ul>
                <li><strong>Capture Deadline:</strong> {$deadlineFormatted}</li>
                <li><strong>Time Remaining:</strong> {$remainingTime}</li>
            </ul>
            
            <p><strong>Action Required:</strong> Please capture this payment before the deadline to secure the funds.</p>
            
            <p>You can capture the payment through your admin panel or Stripe dashboard.</p>
            
            <hr>
            <p><small>This is an automated reminder from your Stripe payment module.</small></p>
        </body>
        </html>
        ";
    }

    /**
     * Get the merchant email address
     * 
     * @return string
     */
    private function getMerchantEmail(): string
    {
        // In a real implementation, this should come from the store configuration
        // For now, use a default that can be configured
        global $configuration;
        
        if (isset($configuration['STORE_OWNER_EMAIL_ADDRESS']) && !empty($configuration['STORE_OWNER_EMAIL_ADDRESS'])) {
            return $configuration['STORE_OWNER_EMAIL_ADDRESS'];
        }
        
        // Fallback to a generic admin email
        return 'admin@' . $_SERVER['HTTP_HOST'];
    }

    /**
     * Get the PaymentRepository instance (for controller access)
     * 
     * @return PaymentRepository
     */
    public function getPaymentRepository(): PaymentRepository
    {
        return $this->paymentRepo;
    }
}