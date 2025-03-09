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

namespace RobinTheHood\Stripe\Classes\Controller;

use Exception;
use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Framework\AbstractController;
//use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\RedirectResponse;
use RobinTheHood\Stripe\Classes\Framework\Request;
use RobinTheHood\Stripe\Classes\Framework\Response;
//use RobinTheHood\Stripe\Classes\Repository;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\View\OrderDetailView;

class AdminController extends AbstractController
{
    public const TEMPLATE_PATH = '../vendor-mmlc/robinthehood/stripe/Templates/';

    private StripeConfiguration $config;

    //private Repository $repo;
    private StripeConfig $stripeConfig;

    private PaymentRepository $paymentRepo;

    public function __construct(StripeConfig $stripeConfig, PaymentRepository $paymentRepo)
    {
        parent::__construct();
        $this->stripeConfig = $stripeConfig;
        $this->paymentRepo = $paymentRepo;
        //$this->repo = $container->get(Repository::class);
        //$this->paymentRepo = $container->get(PaymentRepository::class);
    }

    public function invokeGetStripePaymentDetails(Request $request): Response
    {
        // Get parameters
        $orderId = (int) $request->get('order_id');

        // Basic validation
        if (empty($orderId)) {
            return new Response('Invalid order_id', 400);
        }

        $stripeSecretKey = $this->getSecretKey();
        if (empty($stripeSecretKey)) {
            return new Response('Stripe secret key is not set', 500);
        }

        // Retrieve payment intent ID from order through the repository
        // $paymentIntent = $this->repo->getStripePaymentByOrderId($orderId);
        // $paymentIntentId = $paymentIntent['stripe_payment_intent_id'] ?? null;

        $paymentIntent = $this->paymentRepo->findByOrderId($orderId);
        $paymentIntentId = $paymentIntent['stripe_payment_intent_id'] ?? null;

        if (empty($paymentIntentId)) {
            return new Response('No payment intent found for this order', 404);
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecretKey);
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            // Get charge if available
            $charge = null;
            if (isset($paymentIntent->latest_charge)) {
                $charge = \Stripe\Charge::retrieve(
                    [
                        'id' => $paymentIntent->latest_charge,
                        'expand' => ['payment_method_details', 'balance_transaction', 'refunds'],
                    ]
                );
            }

            // Create the view model
            $view = new OrderDetailView($paymentIntent, $charge);

            // Start output buffering to capture the template output
            ob_start();
            include self::TEMPLATE_PATH . 'StripeDetail.tmpl.php';
            $html = ob_get_clean();

            return new Response($html);
        } catch (Exception $e) {
            $html = '<h3>Stripe - Zahlungsinformationen</h3>';
            $html .= '<div class="error-message">';
            $html .= '<strong>Fehler beim Laden der Zahlungsinformationen</strong><br>';
            $html .= 'Die Stripe-Daten konnten nicht geladen werden: ' . htmlspecialchars($e->getMessage());
            $html .= '</div>';

            // Log the error
            error_log('Stripe API Error: ' . $e->getMessage());

            return new Response($html, 500);
        }
    }

    public function invokeCapture(Request $request): Response
    {
        // Get parameters
        $orderId = (int) $request->get('order_id');
        $paymentIntentId = $request->get('payment_intent_id');

        // Basic validation
        if (empty($orderId) || empty($paymentIntentId)) {
            return new Response('Invalid parameters', 400);
        }

        $stripeSecretKey = $this->getSecretKey();
        if (empty($stripeSecretKey)) {
            return new Response('Stripe secret key is not set', 500);
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecretKey);
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            // Handle form submission for capture
            if ($request->isMethod('POST')) {
                $captureAmount = (float) $request->post('capture_amount');
                if ($captureAmount <= 0) {
                    throw new Exception('Invalid capture amount');
                }

                // Convert amount to cents/smallest currency unit
                $amountInSmallestUnit = (int) ($captureAmount * 100);

                // Perform the capture
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                $paymentIntent->capture(
                    [
                        'amount_to_capture' => $amountInSmallestUnit,
                    ]
                );

                // Redirect back to the order detail page
                return new RedirectResponse("orders.php?oID=$orderId&action=edit");
            }

            // Display the capture form
            $view = new OrderDetailView($paymentIntent);

            // Start output buffering to capture the template output
            ob_start();
            include self::TEMPLATE_PATH . 'CaptureForm.tmpl.php';
            $html = ob_get_clean();

            return new Response($html);
        } catch (Exception $e) {
            $html = $this->renderErrorTemplate('Fehler beim Capture-Vorgang', $e->getMessage());
            return new Response($html, 500);
        }
    }

    public function invokeRefund(Request $request): Response
    {
        // Get parameters
        $orderId = (int) $request->get('order_id');
        $paymentIntentId = $request->get('payment_intent_id');

        // Basic validation
        if (empty($orderId) || empty($paymentIntentId)) {
            return new Response('Invalid parameters', 400);
        }

        $stripeSecretKey = $this->getSecretKey();
        if (empty($stripeSecretKey)) {
            return new Response('Stripe secret key is not set', 500);
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecretKey);
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            $charge = null;

            if (isset($paymentIntent->latest_charge)) {
                $charge = \Stripe\Charge::retrieve(
                    [
                        'id' => $paymentIntent->latest_charge,
                        'expand' => ['payment_method_details', 'balance_transaction', 'refunds'],
                    ]
                );
            } else {
                throw new Exception('No charge found for this payment intent');
            }

            // Calculate refundable amount
            $refundableAmount = $this->calculateRefundableAmount($charge);

            // Handle form submission for refund
            if ($request->isMethod('POST')) {
                $refundAmount = (float) $request->post('refund_amount');
                $refundReason = $request->post('refund_reason') ?: null;

                if ($refundAmount <= 0 || $refundAmount > $refundableAmount) {
                    throw new Exception('Invalid refund amount');
                }

                // Convert amount to cents/smallest currency unit
                $amountInSmallestUnit = (int) ($refundAmount * 100);

                // Perform the refund
                $refundParams = [
                    'charge' => $charge->id,
                    'amount' => $amountInSmallestUnit,
                ];

                if ($refundReason) {
                    $refundParams['reason'] = $refundReason;
                }

                \Stripe\Refund::create($refundParams);

                // Redirect back to the order detail page
                return new RedirectResponse("orders.php?oID=$orderId&action=edit");
            }

            // Display the refund form
            $view = new OrderDetailView($paymentIntent, $charge);
            $view->setRefundableAmount($refundableAmount);

            // Start output buffering to capture the template output
            ob_start();
            include self::TEMPLATE_PATH . 'RefundForm.tmpl.php';
            $html = ob_get_clean();

            return new Response($html);
        } catch (Exception $e) {
            $html = $this->renderErrorTemplate('Fehler beim Rückerstattungs-Vorgang', $e->getMessage());
            return new Response($html, 500);
        }
    }

    public function invokeCancel(Request $request): Response
    {
        // Get parameters
        $orderId = (int) $request->get('order_id');
        $paymentIntentId = $request->get('payment_intent_id');

        // Basic validation
        if (empty($orderId) || empty($paymentIntentId)) {
            return new Response('Invalid parameters', 400);
        }

        $stripeSecretKey = $this->getSecretKey();
        if (empty($stripeSecretKey)) {
            return new Response('Stripe secret key is not set', 500);
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecretKey);

            // Handle form submission for cancellation
            if ($request->isMethod('POST') && $request->post('confirm_cancel') === 'yes') {
                // Perform the cancellation
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                $paymentIntent->cancel();

                // Redirect back to the order detail page
                return new RedirectResponse("orders.php?oID=$orderId&action=edit");
            }

            // Display the cancellation confirmation form
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            $view = new OrderDetailView($paymentIntent);

            // Start output buffering to capture the template output
            ob_start();
            include self::TEMPLATE_PATH . 'CancelForm.tmpl.php';
            $html = ob_get_clean();

            return new Response($html);
        } catch (Exception $e) {
            $html = $this->renderErrorTemplate('Fehler beim Stornieren der Zahlung', $e->getMessage());
            return new Response($html, 500);
        }
    }

    /**
     * Calculates the amount that can still be refunded for a charge
     *
     * @param \Stripe\Charge $charge The Stripe charge object
     * @return float The amount that can still be refunded in the currency's main unit (e.g. dollars)
     */
    private function calculateRefundableAmount(\Stripe\Charge $charge): float
    {
        // Get the total charge amount in smallest currency unit (e.g. cents)
        $chargeAmount = $charge->amount;

        // Calculate already refunded amount
        $refundedAmount = $charge->amount_refunded;

        // Calculate the remaining refundable amount
        $refundableAmountInSmallestUnit = $chargeAmount - $refundedAmount;

        // Convert from smallest currency unit to main unit (e.g. cents to dollars)
        $refundableAmount = $refundableAmountInSmallestUnit / 100;

        return max(0, $refundableAmount);
    }

    private function renderErrorTemplate(string $title, string $message): string
    {
        $html = '<div class="rth-stripe-error-container">';
        $html .= '<h3>' . htmlspecialchars($title) . '</h3>';
        $html .= '<div class="error-message">';
        $html .= htmlspecialchars($message);
        $html .= '</div>';
        $html .= '<div class="actions" style="margin-top: 20px;">';
        $html .= '<a href="javascript:history.back()" class="rth-stripe-button" style="background-color: #6c757d;">Zurück</a>';
        $html .= '</div>';

        return $html;
    }

    private function getSecretKey(): string
    {
        if ($this->stripeConfig->getLiveMode()) {
            return $this->stripeConfig->getApiLiveSecret();
        } else {
            return $this->stripeConfig->getApiSandboxSecret();
        }
    }
}
