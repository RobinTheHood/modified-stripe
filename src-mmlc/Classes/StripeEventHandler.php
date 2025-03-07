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

namespace RobinTheHood\Stripe\Classes;

use Exception;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\Order;
use RobinTheHood\Stripe\Classes\Session as PhpSession;
use Stripe\Event;

class StripeEventHandler
{
    private const RECONSTRUCT_SESSION_TIMEOUT = 60 * 60;

    // StatusId 2 is a default modified status 'Processing'
    private const DEFAULT_ORDER_STATUS_PAID = 2;
    private const DEFAULT_ORDER_STATUS_AUTHORIZED = 1;

    /** @var int */
    private $orderStatusPaid = 2;

    /** @var int */
    private $orderStatusAuthorized = 1;

    private DIContainer $container;

    public function __construct(DIContainer $container)
    {
        $this->container = $container;

        $config = new StripeConfiguration('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');

        $this->orderStatusPaid = $config->getOrderStatusPaid(self::DEFAULT_ORDER_STATUS_PAID);
        $this->orderStatusAuthorized = $config->getOrderStatusAuthorized(self::DEFAULT_ORDER_STATUS_AUTHORIZED);
    }

    /**
     * Handles the Strip WebHook Even checkout.session.completed
     *
     * The main task of this method is to check whether the order has been paid and to set the status on the order to
     * paid.
     *
     * @link https://stripe.com/docs/api/events/types#event_types-checkout.session.completed
     *
     * @param Event $event A Strip Event
     */
    public function checkoutSessionCompleted(Event $event): bool
    {
        $session           = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        $paymentIntentId   = $session->payment_intent;
        $phpSessionId      = $clientReferenceId;

        $order = $this->getOrderBySessionId($phpSessionId);

        if (!$order) {
            error_log("Can not handle stripe event {$event->type} - order is null");
            return false;
        }

        /** @var Repository $repo */
        $repo = $this->container->get(Repository::class);

        // Create a link between the order and the payment regardless of payment status
        $repo->insertRthStripePayment($order->getId(), $paymentIntentId);

        // Only update order status and history if payment status is 'paid'
        if ('paid' === $session->payment_status) {
            $messageData = [
                "id"       => $event->id,
                "object"   => $event->object,
                "created"  => $event->created,
                "livemode" => $event->livemode,
                "type"     => $event->type,
                'payment_intent_id' => $paymentIntentId,
            ];

            $repo->updateOrderStatus($order->getId(), $this->orderStatusPaid);
            $repo->insertOrderStatusHistory($order->getId(), $this->orderStatusPaid, json_encode($messageData, JSON_PRETTY_PRINT));
        }

        $phpSession = $this->container->get(PhpSession::class);
        $phpSession->removeAllExpiredSessions(self::RECONSTRUCT_SESSION_TIMEOUT);
        return true;
    }

    public function checkoutSessionExpired(Event $event): bool
    {
        $session           = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        $phpSessionId      = $clientReferenceId;

        if ('expired' !== $session->status) {
            return false;
        }

        $order = $this->getOrderBySessionId($phpSessionId);

        if (!$order) {
            error_log("Can not handle stripe event {$event->type} - order is null");
            return false;
        }

        Order::removeOrder($order->getId(), true, true);

        return true;
    }

    /**
     * Handles the Stripe WebHook Event payment_intent.amount_capturable_updated
     *
     * This event is triggered when a PaymentIntent with manual capture is ready for capture.
     * The main task is to update the order status to indicate the payment is authorized.
     *
     * @link https://stripe.com/docs/api/events/types#event_types-payment_intent.amount_capturable_updated
     *
     * @param Event $event A Stripe Event
     */
    public function paymentIntentAmountCapturableUpdated(Event $event): bool
    {
        $paymentIntent = $event->data->object;
        $paymentIntentId = $paymentIntent->id;

        if ('requires_capture' !== $paymentIntent->status) {
            return false;
        }

        $orderId = null;

        // Try to get order ID from metadata
        if (isset($paymentIntent->metadata->order_id)) {
            $orderId = (int) $paymentIntent->metadata->order_id;
        } else {
            /** @var Repository $repo */
            $repo = $this->container->get(Repository::class);
            $orderId = $repo->getOrderIdByPaymentIntentId($paymentIntentId);
        }

        if (!$orderId) {
            error_log("Can not handle stripe event {$event->type} - no order found for paymentIntentId {$paymentIntentId}");
            return false;
        }

        // Rest of your code...
        $messageData = [
            "id"       => $event->id,
            "object"   => $event->object,
            "created"  => $event->created,
            "livemode" => $event->livemode,
            "type"     => $event->type,
            'payment_intent_id' => $paymentIntentId,
        ];

        // Update the order status to authorized
        $repo = $this->container->get(Repository::class);
        $repo->updateOrderStatus($orderId, $this->orderStatusAuthorized);
        $repo->insertOrderStatusHistory($orderId, $this->orderStatusAuthorized, json_encode($messageData, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * Retrieves an order by PHP session ID
     *
     * @param string $phpSessionId The PHP session ID
     * @return Order|null The order if found, null otherwise
     */
    private function getOrderBySessionId(string $phpSessionId): ?Order
    {
        try {
            /** @var PhpSession $phpSession */
            $phpSession = $this->container->get(PhpSession::class);
            $phpSession->load($phpSessionId);
            return $phpSession->getOrder();
        } catch (Exception $e) {
            error_log("Failed to retrieve order from session - " . $e->getMessage());
            return null;
        }
    }
}
