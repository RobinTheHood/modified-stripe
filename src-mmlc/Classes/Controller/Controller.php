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
use RobinTheHood\Stripe\Classes\Framework\AbstractController;
use RobinTheHood\Stripe\Classes\Framework\RedirectResponse;
use RobinTheHood\Stripe\Classes\Framework\Request;
use RobinTheHood\Stripe\Classes\Framework\Response;
use RobinTheHood\Stripe\Classes\Framework\SplashMessage;
use RobinTheHood\Stripe\Classes\Routing\UrlBuilder;
use RobinTheHood\Stripe\Classes\Service\CheckoutService;
use RobinTheHood\Stripe\Classes\Service\PaymentCaptureService;
use RobinTheHood\Stripe\Classes\Service\SessionService;
use RobinTheHood\Stripe\Classes\Service\WebhookService;
use RobinTheHood\Stripe\Classes\Service\CaptureReminderService;

class Controller extends AbstractController
{
    private CheckoutService $checkoutService;
    private SessionService $sessionService;
    private WebhookService $webhookService;
    private PaymentCaptureService $captureService;
    private CaptureReminderService $reminderService;
    private UrlBuilder $urlBuilder;

    public function __construct(
        CheckoutService $checkoutService,
        SessionService $sessionService,
        WebhookService $webhookService,
        PaymentCaptureService $captureService,
        CaptureReminderService $reminderService,
        UrlBuilder $urlBuilder
    ) {
        parent::__construct();

        $this->checkoutService = $checkoutService;
        $this->sessionService = $sessionService;
        $this->webhookService = $webhookService;
        $this->captureService = $captureService;
        $this->reminderService = $reminderService;
        $this->urlBuilder = $urlBuilder;
    }

    protected function invokeIndex(Request $request): Response
    {
        return new Response('There is nothing to do');
    }

    protected function invokeCheckout(): Response
    {
        try {
            $session = $this->checkoutService->createCheckoutSession();
            return new RedirectResponse($session->url);
        } catch (Exception $e) {
            $splashMessage = SplashMessage::getInstance();
            $splashMessage->error('shopping_cart', 'Can not create Stripe Checkout Session.');
            return new RedirectResponse($this->urlBuilder->getShoppingCart());
        }
    }

    protected function invokeSuccess(Request $request): Response
    {
        try {
            $stripeSessionId = $request->get('session_id');
            $this->sessionService->processSuccessfulCheckout($stripeSessionId);
            return new RedirectResponse($this->urlBuilder->getCheckoutProcess());
        } catch (Exception $e) {
            $splashMessage = SplashMessage::getInstance();
            $splashMessage->error('shopping_cart', $e->getMessage());
            return new RedirectResponse($this->urlBuilder->getShoppingCart());
        }
    }

    public function invokeCancel(): Response
    {
        return new RedirectResponse($this->urlBuilder->getCheckoutConfirmation() . '?conditions=true');
    }

    protected function invokeReceiveHook(Request $request): Response
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->getServer('HTTP_STRIPE_SIGNATURE');

            $success = $this->webhookService->processWebhook($payload, $sigHeader);
            return new Response('', $success ? 200 : 400);
        } catch (\UnexpectedValueException $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response(json_encode(['error' => $e->getMessage()]), 400);
        }
    }

    protected function invokeCapture(Request $request): Response
    {
        $orderId = $request->get('order_id');
        if (!$orderId) {
            return new Response('Missing order_id parameter', 400);
        }

        try {
            $this->captureService->capturePayment((int) $orderId);

            $splashMessage = SplashMessage::getInstance();
            $splashMessage->addAdminMessage('Zahlung erfolgreich eingenommen.', SplashMessage::TYPE_SUCCESS);

            return new RedirectResponse($this->urlBuilder->getAdminOrders() . '?oID=' . $orderId . '&action=edit');
        } catch (Exception $e) {
            $splashMessage = SplashMessage::getInstance();
            $splashMessage->addAdminMessage(
                'Fehler beim Einnehmen der Zahlung: ' . $e->getMessage(),
                SplashMessage::TYPE_ERROR
            );

            return new RedirectResponse($this->urlBuilder->getAdminOrders() . '?oID=' . $orderId . '&action=edit');
        }
    }

    /**
     * Check for payments nearing capture deadline and send reminder emails
     * This action can be called via cron job: /rth_stripe.php?action=checkCaptureReminders
     */
    protected function invokeCheckCaptureReminders(Request $request): Response
    {
        try {
            $remindersSent = $this->reminderService->checkAndSendReminders();
            
            return new Response(
                json_encode([
                    'success' => true,
                    'reminders_sent' => $remindersSent,
                    'message' => "Checked capture reminders. Sent {$remindersSent} reminder(s)."
                ]),
                200
            );
        } catch (Exception $e) {
            return new Response(
                json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]),
                500
            );
        }
    }

    /**
     * Update database schema for reminder functionality
     * This action can be called once after module update: /rth_stripe.php?action=updateSchema
     */
    protected function invokeUpdateSchema(Request $request): Response
    {
        try {
            // Update the payment table schema
            $paymentRepo = $this->reminderService->getPaymentRepository();
            $paymentRepo->updateTableSchema();
            
            return new Response(
                json_encode([
                    'success' => true,
                    'message' => 'Database schema updated successfully.'
                ]),
                200
            );
        } catch (Exception $e) {
            return new Response(
                json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]),
                500
            );
        }
    }
}
