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

    /** @var int */
    private $orderStatusPaid = 2;

    private DIContainer $container;

    public function __construct(DIContainer $container)
    {
        $this->container = $container;

        $config = new StripeConfiguration('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');
        $this->orderStatusPaid = $config->getOrderStatusPaid(self::DEFAULT_ORDER_STATUS_PAID);
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
        $session = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        $paymentIntentId = $session->payment_intent;
        $phpSessionId = $clientReferenceId;


        if ('paid' !== $session->payment_status) {
            return false;
        }

        try {
            /** @var PhpSession $phpSession */
            $phpSession = $this->container->get(PhpSession::class);
            $phpSession->load($phpSessionId);
        } catch (Exception $e) {
            error_log("Can not handle stripe event {$event->type} - " . $e->getMessage());
            return false;
        }

        $order = $phpSession->getOrder();

        if (!$order) {
            error_log("Can not handle stripe event {$event->type} - order is null");
            return false;
        }

        $messageData = [
            "id"       => $event->id,
            "object"   => $event->object,
            "created"  => $event->created,
            "livemode" => $event->livemode,
            "type"     => $event->type
        ];

        /** @var Repository $repo */
        $repo = $this->container->get(Repository::class);
        $repo->updateOrderStatus($order->getId(), $this->orderStatusPaid);
        $repo->insertOrderStatusHistory($order->getId(), $this->orderStatusPaid, json_encode($messageData, JSON_PRETTY_PRINT));

        // Create a link between the order and the payment
        $repo->insertRthStripePayment($order->getId(), $paymentIntentId);

        $phpSession->removeAllExpiredSessions(self::RECONSTRUCT_SESSION_TIMEOUT);
        return true;
    }

    public function checkoutSessionExpired(Event $event): bool
    {
        $session = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        $phpSessionId = $clientReferenceId;

        if ('expired' !== $session->status) {
            return false;
        }

        try {
            $phpSession = $this->container->get(Session::class);
            $phpSession->load($phpSessionId);
        } catch (Exception $e) {
            error_log("Can not handle stripe event {$event->type} - " . $e->getMessage());
            return false;
        }

        $order = $phpSession->getOrder();

        if (!$order) {
            error_log("Can not handle stripe event {$event->type} - order is null");
            return false;
        }

        Order::removeOrder($order->getId(), true, true);

        return true;
    }
}
