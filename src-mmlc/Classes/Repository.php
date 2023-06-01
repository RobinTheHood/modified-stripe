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

/**
 * In this class we outsource all queries to the database. So we only need SQL in this file.
 */
class Repository
{
    public function createRthStripePhpSession()
    {
        xtc_db_query("CREATE TABLE `rth_stripe_php_session` (
            `id` varchar(32) NOT NULL,
            `created` datetime DEFAULT NULL,
            `data` longtext DEFAULT NULL,
            PRIMARY KEY (`id`)
          );
        ");
    }

    public function getRthStripePhpSessionById(string $id)
    {
        $sql = "SELECT * FROM rth_stripe_php_session WHERE id='$id'";

        $query = xtc_db_query($sql);

        $row = xtc_db_fetch_array($query);
        if (!isset($row['id'])) {
            return false;
        }

        return $row;
    }

    public function insertRthStripePhpSession(string $id, string $data)
    {
        $sql = "INSERT INTO rth_stripe_php_session (
            `id`, `data`, `created`
        ) VALUES (
            '$id', '$data', NOW()
        )";

        xtc_db_query($sql);
    }

    public function updateOrderStatus(int $orderId, int $statusId): void
    {
        $sql = "UPDATE `orders` SET `orders_status` = '$statusId', `last_modified` = NOW() WHERE orders_id = '$orderId'";
        xtc_db_query($sql);
    }

    public function insertOrderStatusHistory(int $orderId, int $statusId, string $comment = '')
    {
        $sql = "INSERT INTO orders_status_history (
            `orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`, `comments_sent`
        ) VALUES (
            '$orderId', '$statusId', NOW(), '0', '$comment', '0'
        )";
        xtc_db_query($sql);
    }
}
