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

class Constant
{
    /**
     * @return string HTTPS_SERVER defined in inlcudes/configuration.php
     *      Example: https://example.com
     */
    public static function getHttpsServer(): string
    {
        return defined('HTTPS_SERVER') ? constant('HTTPS_SERVER') : '';
    }
}