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

class Field
{
    public static function apiSandboxKey($value, $option): string
    {
        $pattern = '^pk_test_[a-zA-Z0-9]+$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" />
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function apiSandboxSecret($value, $option): string
    {
        $pattern = '^sk_test_[a-zA-Z0-9]+$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" />
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function apiLiveKey($value, $option): string
    {
        /**
         * Once we know how the live api key looks, we can make this regex
         * more precise.
         */
        $pattern = '[a-zA-Z_0-9]+';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" />
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function apiLiveSecret($value, $option): string
    {
        /**
         * Once we know how the live api secret looks, we can make this regex
         * more precise.
         */
        $pattern = '[a-zA-Z_0-9]+';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" />
        <?php
        $field = ob_get_clean();

        return $field;
    }
}
