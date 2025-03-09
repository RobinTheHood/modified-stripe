<?php

/**
 * Stripe integration for modified
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Repository;

use RobinTheHood\Stripe\Classes\Framework\Database;

class PaymentRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function createTable(): void
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `rth_stripe_payment` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `created` datetime DEFAULT NULL,
                `order_id` int(11) DEFAULT NULL,
                `stripe_payment_intent_id` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            );"
        );
    }

    public function add(int $orderId, string $stripePaymentIntentId): int
    {
        $dateTime = new \DateTime();
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $this->db->query(
            "INSERT INTO rth_stripe_payment (
                `created`, `order_id`, `stripe_payment_intent_id`
            ) VALUES (
                '$formattedDateTime', '$orderId', '$stripePaymentIntentId'
            )"
        );

        return $this->db->getLastInsertId();
    }

    public function findByOrderId(int $orderId): array|false
    {
        $query = $this->db->query(
            "SELECT * FROM rth_stripe_payment WHERE order_id = $orderId ORDER BY created DESC LIMIT 1"
        );

        $row = $this->db->fetch($query);
        if (!isset($row['id'])) {
            return false;
        }

        return $row;
    }


    public function findByStripePaymentIntentId(string $paymentIntentId): array|false
    {
        $query = $this->db->query(
            "SELECT * FROM rth_stripe_payment 
            WHERE stripe_payment_intent_id = '$paymentIntentId' 
            ORDER BY created DESC LIMIT 1"
        );

        $row = $this->db->fetch($query);
        if (!isset($row['id'])) {
            return false;
        }

        return $row;
    }
}
