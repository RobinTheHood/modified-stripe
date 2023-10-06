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

    public static function apiLiveEndpointSecret($value, $option): string
    {
        /**
         * Once we know how the live api secret looks, we can make this regex
         * more precise.
         */
        $pattern = '^(whsec_[a-zA-Z_0-9]+)?$';

        ob_start()
        ?>
        <input type="text" name="configuration[<?= $option ?>]" pattern="<?= $pattern ?>" value="<?= $value ?>" placeholder="whsec_..."/>
        <?php
        $field = ob_get_clean();

        return $field;
    }

    public static function checkoutTitleDesc(string $value, string $option): string
    {
        require_once 'includes/functions/general.php';

        $languages = xtc_get_languages();

        ob_start();
        ?>

        <div class="tabs">
            <ul class="navigation">
                <?php foreach ($languages as $language) { ?>
                    <li>
                        <label for="<?= $option . '-' . $language['code']; ?>">
                            <?= xtc_image(DIR_WS_LANGUAGES . $language['directory'] . '/admin/images/' . $language['image'], $language['name']); ?>

                            <?= $language['name'] ?>
                        </label>
                    </li>
                <?php } ?>
            </ul>
            <ul class="content">
                <?php foreach ($languages as $language) { ?>
                    <li>
                        <input type="radio" name="tab" id="<?= $option . '-' . $language['code']; ?>" />

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
