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
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\Request;
use RobinTheHood\Stripe\Classes\Framework\Response;
use RobinTheHood\Stripe\Classes\Repository;
use RobinTheHood\Stripe\Classes\StripeConfiguration;
use RobinTheHood\Stripe\Classes\View\OrderDetailView;

class AdminController extends AbstractController
{
    public const TEMPLATE_PATH = '../vendor-mmlc/robinthehood/stripe/Templates/';

    private StripeConfiguration $config;

    private Repository $repo;

    public function __construct(DIContainer $container)
    {
        parent::__construct();
        $this->config = $container->get(StripeConfiguration::class);
        $this->repo = $container->get(Repository::class);
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
        $paymentIntent = $this->repo->getStripePaymentByOrderId($orderId);
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
                        'expand' => ['payment_method_details'],
                    ]
                );
            }

            // Create the view model
            $view = new OrderDetailView($paymentIntent, $charge);

            // Start output buffering to capture the template output
            ob_start();
            include self::TEMPLATE_PATH . 'OrderDetail.tmpl.php';
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

    private function getSecretKey(): string
    {
        if ($this->config->getLiveMode()) {
            return $this->config->getApiLiveSecret();
        } else {
            return $this->config->getApiSandboxSecret();
        }
    }
}
