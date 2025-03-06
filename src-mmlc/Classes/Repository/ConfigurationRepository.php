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

class ConfigurationRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function updateValue(string $key, string $value): void
    {
        $this->db->query(
            "UPDATE `configuration` SET configuration_value = '$value'
                WHERE configuration_key = '$key';"
        );
    }

    public function findByKey(string $key): array|false
    {
        $query = $this->db->query(
            "SELECT * FROM configuration WHERE configuration_key = '$key'"
        );

        $row = $this->db->fetch($query);
        if (!isset($row['configuration_id'])) {
            return false;
        }

        return $row;
    }
}
