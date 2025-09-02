<?php

/**
 * Stripe integration for modified
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Repository;

use RobinTheHood\Stripe\Classes\Framework\Database;

class ActionLogRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Creates the log table if it does not exist.
     * Table: rth_stripe_action_log
     */
    public function createTable(): void
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `rth_stripe_action_log` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `created` datetime DEFAULT NULL,
                `type` varchar(64) NOT NULL,
                `reference` varchar(255) NOT NULL,
                `data` longtext DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_type_reference` (`type`,`reference`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        );
    }

    /**
     * Adds a new action log entry.
     * Returns inserted id (or 0 on error fallback).
     */
    public function add(string $type, string $reference, array $data = []): int
    {
        $dateTime = new \DateTime();
        $created = $dateTime->format('Y-m-d H:i:s');
        $json = $this->escapeJson($data);

        $typeEsc = $this->escape($type);
        $refEsc = $this->escape($reference);

        $this->db->query(
            "INSERT INTO rth_stripe_action_log (`created`, `type`, `reference`, `data`) VALUES (
                '$created', '$typeEsc', '$refEsc', '$json'
            )"
        );

        return $this->db->getLastInsertId();
    }

    /**
     * Checks if an entry for given type+reference exists.
     */
    public function exists(string $type, string $reference): bool
    {
        $typeEsc = $this->escape($type);
        $refEsc = $this->escape($reference);
        $query = $this->db->query(
            "SELECT id FROM rth_stripe_action_log WHERE type='$typeEsc' AND reference='$refEsc' LIMIT 1"
        );
        $row = $this->db->fetch($query);
        return isset($row['id']);
    }

    /**
     * Find latest entry by type, ordered by created desc.
     * Returns row array or false.
     */
    public function findLastByType(string $type): array|false
    {
        $typeEsc = $this->escape($type);
        $query = $this->db->query(
            "SELECT * FROM rth_stripe_action_log WHERE type='$typeEsc' ORDER BY created DESC, id DESC LIMIT 1"
        );
        $row = $this->db->fetch($query);
        if (!isset($row['id'])) {
            return false;
        }
        return $row;
    }

    private function escape(string $value): string
    {
        // Use basic addslashes for consistency with existing repository pattern (no PDO).
        return addslashes($value);
    }

    private function escapeJson(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (false === $json) {
            $json = '{}';
        }
        return addslashes($json);
    }
}
