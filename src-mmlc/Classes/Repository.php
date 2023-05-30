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
}
