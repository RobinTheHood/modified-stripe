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

class SessionRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function createTable(): void
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

    public function findById(string $id): array|false
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

    public function findAllExpired(int $expiresAt): array
    {
        $dateTime = new \DateTime();
        $dateTime->modify("-$expiresAt seconds");
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $query = $this->db->query(
            "SELECT * FROM rth_stripe_php_session WHERE created < '$formattedDateTime';"
        );

        return $this->db->fetchAll($query);
    }

    public function add(string $id, string $data): int
    {
        $dateTime = new \DateTime();
        $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

        $this->db->query(
            "INSERT INTO rth_stripe_php_session (
                `id`, `data`, `created`
            ) VALUES (
                '$id', '$data', '$formattedDateTime'
            )"
        );

        return $this->db->getLastInsertId();
    }

    public function deleteById(string $id): void
    {
        $this->db->query(
            "DELETE FROM rth_stripe_php_session WHERE id='$id'"
        );
    }
}
