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

namespace RobinTheHood\Stripe\Classes\Framework;

use Exception;
use RobinTheHood\Stripe\Classes\Repository;
use RobinTheHood\Stripe\Classes\Session;

class DIContainer
{
    public function get(string $class)
    {
        if (Session::class === $class) {
            return new Session(new Repository(new Database()));
        } elseif (Repository::class === $class) {
            return new Repository(new Database());
        }

        throw new Exception('Can not create object of type ' . $class);
    }
}
