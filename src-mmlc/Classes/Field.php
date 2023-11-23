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

use RobinTheHood\Stripe\Classes\Framework\Constant;

/**
 * This class is used to render individual input fields in the payment_rth_stripe class, which the user sees in
 * the shop admin, where he can make the settings for the strip payment module.
 *
 * @see payment_rth_stripe.php
 */
class Field
{
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
        <input id="apiSandboxSerect" type="password" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="sk_test_..."/>
        <input type="checkbox" onchange="const apiSandboxSerectInput = document.getElementById('apiSandboxSerect'); if (this.checked) {apiSandboxSerectInput.setAttribute('type', 'text')} else {apiSandboxSerectInput.setAttribute('type', 'password')}"> anzeigen
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
        <input id="apiSerect" type="password" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="sk_live_..."/>
        <input type="checkbox" onchange="const apiSerectInput = document.getElementById('apiSerect'); if (this.checked) {apiSerectInput.setAttribute('type', 'text')} else {apiSerectInput.setAttribute('type', 'password')}"> anzeigen
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function apiLiveEndpointSecret($value, $option): string
    {
        /**
         * Once we know how the live api secret looks, we can make this regex
         * more precise.
         */
        $pattern = '^(whsec_[a-zA-Z_0-9]+)?$';

        ob_start()
        ?>
        <input id="webhookSerect" type="password" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="whsec_..."/>
        <input type="checkbox" onchange="const webhookSerectInput = document.getElementById('webhookSerect'); if (this.checked) {webhookSerectInput.setAttribute('type', 'text')} else {webhookSerectInput.setAttribute('type', 'password')}"> anzeigen
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function checkoutTitleDesc(string $value, string $option): string
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
}
