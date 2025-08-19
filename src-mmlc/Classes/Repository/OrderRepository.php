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
     * Get the maximum order ID from the orders table.
     * This is used to reset the auto-increment counter.
     */
    public function getMaxOrderId(): int
    {
        $query = $this->db->query("SELECT MAX(orders_id) as max_id FROM `orders`");
        $row = $this->db->fetch($query);
        
        return $row && isset($row['max_id']) ? (int)$row['max_id'] : 0;
    }

    /**
     * Reset the auto-increment value for the orders table to the next value after the maximum order ID.
     * This prevents gaps in order numbers after temporary orders are deleted.
     */
    public function resetAutoIncrement(): void
    {
        $maxId = $this->getMaxOrderId();
        $nextId = $maxId + 1;
        
        $this->db->query("ALTER TABLE `orders` AUTO_INCREMENT = $nextId");
    }
}
