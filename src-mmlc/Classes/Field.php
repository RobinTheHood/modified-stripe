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
 * This class is used to render individual input fields in the payment_rth_stripe class, which the user sees in
 * the shop admin, where he can make the settings for the strip payment module.
 *
 * @see payment_rth_stripe.php
 */
class Field
{
    /**
     * Returns the `setFunction` name for the specified `configurationKey`.
     *
     * Namespaces are encoded in base64 since the backward slashes will
     * otherwise be removed before saving. The `setFunction` method will decode
     * the namespaces and forward all data.
     *
     * @see PaymentModule::setFunction()
     *
     * @param string $class            The class name containing the
     *                                 `setFunction`.
     * @param string $configurationKey The key to get the `setFunction` value
     *                                 for.
     *
     * @return string The `setFunction` name for the specified `configurationKey`.
     */
    public static function getSetFunction(string $configurationKey): string
    {
        $class            = substr(payment_rth_stripe::class, strlen(__NAMESPACE__) + 1);
        $setFunction      = $class . '::setFunction(\'%s\',';
        $setFunctionField = sprintf(
            $setFunction,
            base64_encode(
                self::class . '::' .  $configurationKey
            )
        );

        return $setFunctionField;
    }

    public static function apiSandboxKey($value, $option): string
    {
        $pattern = '^(pk_test_[a-zA-Z0-9]+)?$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="pk_test_..."/>
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function apiSandboxSecret($value, $option): string
    {
        $pattern = '^(sk_test_[a-zA-Z0-9]+)?$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="sk_test_..."/>
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
        $pattern = '^(pk_live_[a-zA-Z_0-9]+)?$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="pk_live_..."/>
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
        $pattern = '^(sk_live_[a-zA-Z_0-9]+)?$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="sk_live_..."/>
        <?php
        $field = ob_get_clean();

        return $field;
    }
}
