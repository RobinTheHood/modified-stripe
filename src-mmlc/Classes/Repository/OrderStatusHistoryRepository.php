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

class OrderStatusHistoryRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function add(int $orderId, int $statusId, string $comment = ''): int
    {
        $dateTime = new \DateTime();
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $this->db->query(
            "INSERT INTO orders_status_history (
                `orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`, `comments_sent`
            ) VALUES (
                '$orderId', '$statusId', '$formattedDateTime', '0', '$comment', '0'
            )"
        );

        return $this->db->getLastInsertId();
    }
}
