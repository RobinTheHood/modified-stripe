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

namespace RobinTheHood\Stripe\Classes\UI;

use RobinTheHood\Stripe\Classes\Framework\Constant;

/**
 * This class is used to render individual input fields in the payment_rth_stripe class, which the user sees in
 * the shop admin, where he can make the settings for the strip payment module.
 *
 * @see payment_rth_stripe.php
 */
class ConfigurationFieldRenderer
{
    /**
     * Renders a text input field for the Stripe sandbox API key.
     */
    public static function apiSandboxKey($value, $option): string
    {
        return self::renderTextField(
            $value,
            $option,
            '^(pk_test_[a-zA-Z0-9]+)?$',
            'pk_test_...'
        );
    }

    /**
     * Renders a password input field for the Stripe sandbox API secret.
     */
    public static function apiSandboxSecret($value, $option): string
    {
        return self::renderPasswordField(
            $value,
            $option,
            '^(sk_test_[a-zA-Z0-9]+)?$',
            'sk_test_...',
            'apiSandboxSerect'
        );
    }

    /**
     * Renders a text input field for the Stripe live API key.
     */
    public static function apiLiveKey($value, $option): string
    {
        return self::renderTextField(
            $value,
            $option,
            '^(pk_live_[a-zA-Z_0-9]+)?$',
            'pk_live_...'
        );
    }

    /**
     * Renders a password input field for the Stripe live API secret.
     */
    public static function apiLiveSecret($value, $option): string
    {
        return self::renderPasswordField(
            $value,
            $option,
            '^(sk_live_[a-zA-Z_0-9]+)?$',
            'sk_live_...',
            'apiSerect'
        );
    }

    /**
     * Renders a password input field for the Stripe live endpoint secret.
     */
    public static function apiLiveEndpointSecret($value, $option): string
    {
        return self::renderPasswordField(
            $value,
            $option,
            '^(whsec_[a-zA-Z_0-9]+)?$',
            'whsec_...',
            'webhookSerect'
        );
    }

    /**
     * Renders a multi-language input field.
     */
    public static function renderMultiLanguageTextField(string $value, string $option): string
    {
        $languages = xtc_get_languages();

        ob_start();
        ?>
        <div class="rth-stripe-tabs">
            <ul class="navigation">
                <?php foreach ($languages as $language) { ?>
                    <li>
                        <?= xtc_image(Constant::getDirWsLanguages() . $language['directory'] . '/admin/images/' . $language['image'], $language['name']); ?>
                        <?= $language['name'] ?>
                    </li>
                <?php } ?>
            </ul>
            <ul class="content">
                <?php foreach ($languages as $language) { ?>
                    <li>
                        <div class="content">
                            <input type="text" name="configuration[<?= $option . '][' . strtoupper($language['code']) . ']' ?>" value="<?= parse_multi_language_value($value, $language['code'], true) ?>" />
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Helper method to render a text input field.
     */
    private static function renderTextField(string $value, string $option, string $pattern, string $placeholder): string
    {
        ob_start();
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="<?= $placeholder ?>"/>
        <?php
        return ob_get_clean();
    }

    /**
     * Helper method to render a password input field with a toggle to show/hide.
     */
    private static function renderPasswordField(string $value, string $option, string $pattern, string $placeholder, string $id): string
    {
        ob_start();
        ?>
        <input id="<?= $id ?>" type="password" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="<?= $placeholder ?>"/>
        <input type="checkbox" onchange="const input = document.getElementById('<?= $id ?>'); if (this.checked) {input.setAttribute('type', 'text')} else {input.setAttribute('type', 'password')}"> anzeigen
        <?php
        return ob_get_clean();
    }
}