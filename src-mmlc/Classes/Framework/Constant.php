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
     * @return string HTTP_SERVER defined in inlcudes/configure.php
     *      Example: http://example.com
     */
    public static function getHttpServer(): string
    {
        return self::getValue('HTTP_SERVER');
    }

    /**
     * @return string HTTPS_SERVER defined in inlcudes/configure.php
     *      Example: https://example.com
     */
    public static function getHttpsServer(): string
    {
        return self::getValue('HTTPS_SERVER');
    }

    /**
     * @return string DIR_WS_CATALOG defined in inlcudes/configure.php
     *      Example: /
     */
    public static function getDirWsCatalog(): string
    {
        return self::getValue('DIR_WS_CATALOG');
    }

    /**
     * @return string HTTPS_SERVER defined in inlcudes/paths.php
     *      Example: /lang
     */
    public static function getDirWsLanguages(): string
    {
        return self::getValue('DIR_WS_LANGUAGES');
    }

    public static function getDirAdmin(): string
    {
        return self::getValue('DIR_ADMIN');
    }

    private static function getValue(string $name): string
    {
        return defined($name) ? constant($name) : '';
    }
}
