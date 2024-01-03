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

use RobinTheHood\Stripe\Classes\Framework\Database;

/**
 * In this class we outsource all queries to the database. So we only need SQL in this file.
 */
class Repository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function createRthStripePhpSession(): void
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `rth_stripe_php_session` (
                `id` varchar(32) NOT NULL,
                `created` datetime DEFAULT NULL,
                `data` longtext DEFAULT NULL,
                PRIMARY KEY (`id`)
            );"
        );
    }

    public function createRthStripePayment(): void
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

    public function getRthStripePhpSessionById(string $id)
    {
        $query = $this->db->query(
            "SELECT * FROM rth_stripe_php_session WHERE id='$id'"
        );

        $row = $this->db->fetch($query);
        if (!isset($row['id'])) {
            return false;
        }

        return $row;
    }

    public function getAllExpiredRthStripePhpSessions(int $expiresAt): array
    {
        $query = $this->db->query(
            "SELECT * FROM rth_stripe_php_session WHERE created < NOW() - INTERVAL $expiresAt SECOND;"
        );

        return $this->db->fetchAll($query);
    }

    public function insertRthStripePhpSession(string $id, string $data): void
    {
        $this->db->query(
            "INSERT INTO rth_stripe_php_session (
                `id`, `data`, `created`
            ) VALUES (
                '$id', '$data', NOW()
            )"
        );
    }

    public function deleteRthStripePhpSessionById(string $id): void
    {
        $this->db->query(
            "DELETE FROM rth_stripe_php_session WHERE id='$id'"
        );
    }

    public function updateOrderStatus(int $orderId, int $statusId): void
    {
        $this->db->query(
            "UPDATE `orders` SET `orders_status` = '$statusId', `last_modified` = NOW() WHERE orders_id = '$orderId'"
        );
    }

    public function insertOrderStatusHistory(int $orderId, int $statusId, string $comment = ''): void
    {
        $this->db->query(
            "INSERT INTO orders_status_history (
                `orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`, `comments_sent`
            ) VALUES (
                '$orderId', '$statusId', NOW(), '0', '$comment', '0'
            )"
        );
    }

    public function insertRthStripePayment(int $orderId, string $stripePaymentIntentId): void
    {
        $this->db->query(
            "INSERT INTO rth_stripe_payment (
                `created`, `order_id`, `stripe_payment_intent_id`
            ) VALUES (
                NOW(), '$orderId', '$stripePaymentIntentId'
            )"
        );
    }

    public function updateConfigurationValue(string $configurationKey, string $configurationValue): void
    {
        $table = TABLE_CONFIGURATION;
        $this->db->query(
            "UPDATE $table SET configuration_value = '$configurationValue'
                WHERE configuration_key = '$configurationKey';"
        );
    }
}
