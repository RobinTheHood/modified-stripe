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

class OrderRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function updateStatus(int $orderId, int $statusId): void
    {
        $dateTime = new \DateTime();
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $this->db->query(
            "UPDATE `orders` SET `orders_status` = '$statusId', `last_modified` = '$formattedDateTime' WHERE orders_id = '$orderId'"
        );
    }

    /**
     * Find order by order ID
     */
    public function findById(int $orderId): array|false
    {
        $query = $this->db->query(
            "SELECT * FROM orders WHERE orders_id = $orderId LIMIT 1"
        );

        $row = $this->db->fetch($query);
        if (!$row || !isset($row['orders_id'])) {
            return false;
        }

        return $row;
    }
}
