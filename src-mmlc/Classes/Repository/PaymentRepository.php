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
                `reminder_sent_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            );"
        );
    }

    /**
     * Update table schema to add reminder_sent_at column for existing installations
     */
    public function updateTableSchema(): void
    {
        // Check if the column already exists
        $result = $this->db->query("SHOW COLUMNS FROM `rth_stripe_payment` LIKE 'reminder_sent_at'");
        $row = $this->db->fetch($result);
        
        if (!$row) {
            // Column doesn't exist, add it
            $this->db->query(
                "ALTER TABLE `rth_stripe_payment` 
                ADD COLUMN `reminder_sent_at` datetime DEFAULT NULL"
            );
        }
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

    /**
     * Find all payments that have not had reminder emails sent yet
     * 
     * @return array
     */
    public function findPaymentsWithoutReminders(): array
    {
        $query = $this->db->query(
            "SELECT * FROM rth_stripe_payment 
            WHERE reminder_sent_at IS NULL 
            ORDER BY created ASC"
        );

        $results = [];
        while ($row = $this->db->fetch($query)) {
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Mark a payment as having had its reminder email sent
     * 
     * @param int $paymentId
     * @return void
     */
    public function markReminderSent(int $paymentId): void
    {
        $dateTime = new \DateTime();
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $this->db->query(
            "UPDATE rth_stripe_payment 
            SET reminder_sent_at = '$formattedDateTime' 
            WHERE id = $paymentId"
        );
    }
}
