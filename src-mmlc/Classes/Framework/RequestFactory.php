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

class RequestFactory
{
    public static function createFromGlobals(): Request
    {
        return new Request(
            $_GET ?? [],
            $_POST ?? [],
            [],
            [],
            [],
            $_SERVER ?? [],
            @file_get_contents('php://input')
        );
    }
}
