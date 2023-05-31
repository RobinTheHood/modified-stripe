<?php

namespace RobinTheHood\Stripe\Classes\Configuration;

use RobinTheHood\ModifiedStdModule\Classes\StdModule;
use RobinTheHood\Stripe\Classes\Constants;

class Checkout extends StdModule
{
    /**
     * Returns the modified language code for the specified configuration key.
     *
     * @param string $configurationKey The configuration key to retrieve the
     *                                 language code from.
     *
     * @return string The modified language code for the specified configuration
     *                key.
     */
    private static function getLanguageCode(string $configurationKey): string
    {
        $parts        = explode('_', $configurationKey);
        $parts_last   = end($parts);
        $languageCode = $parts_last;

        return $languageCode;
    }

    /**
     * Sets the language constants for dynamically created configuration.
     *
     * @return void
     */
    public static function setLanguageConstants(): void
    {
        foreach (self::getConfigurationKeys() as $configurationKey) {
            $configurationKeyLanguageCode = self::getLanguageCode($configurationKey);
            $configurationKeyWithoutCode  = substr($configurationKey, 0, -1 * (strlen($configurationKeyLanguageCode) + 1));

            $configurationValueTitle = '';
            $configurationValueDesc  = '';

            /** Fallback translations */
            switch ($configurationKeyWithoutCode) {
                case Constants::CONFIGURATION_CHECKOUT_TITLE:
                    $configurationValueTitle = sprintf(
                        'Checkout title (%s)',
                        $configurationKeyLanguageCode
                    );
                    $configurationValueDesc  = sprintf(
                        'Text that appears at the bottom of the Stripe checkout (such as <i>Your purchase at %s</i>)',
                        HTTPS_SERVER
                    );
                    break;

                case Constants::CONFIGURATION_CHECKOUT_DESC:
                    $configurationValueTitle = sprintf(
                        'Checkout description (%s)',
                        $configurationKeyLanguageCode
                    );
                    $configurationValueDesc  = 'Text that appears at the top of the Stripe checkout (such as <i>We thank you for your trust.</i>)';
                    break;
            }

            /** Language specific translations */
            switch ($configurationKey) {
                case Constants::CONFIGURATION_CHECKOUT_TITLE . '_DE':
                    $configurationValueTitle = sprintf(
                        'Checkout Titel (%s)',
                        $configurationKeyLanguageCode
                    );
                    $configurationValueDesc  = sprintf(
                        'Text der im Stripe checkout oben erscheint (wie z. B. <i>Ihr Einkauf bei %s</i>)',
                        HTTP_SERVER
                    );
                    break;

                case Constants::CONFIGURATION_CHECKOUT_DESC . '_DE':
                    $configurationValueTitle = sprintf(
                        'Checkout Beschreibung (%s)',
                        $configurationKeyLanguageCode
                    );
                    $configurationValueDesc  = 'Text der im Stripe checkout unten erscheint (wie z. B. <i>Vielen Dank für Ihre Vertrauen.</i>)';
                    break;
            }

            $constantTitle = Constants::MODULE_PAYMENT_NAME . '_' . $configurationKey . '_TITLE';
            $constantDesc  = Constants::MODULE_PAYMENT_NAME . '_' . $configurationKey . '_DESC';

            define($constantTitle, $configurationValueTitle);
            define($constantDesc, $configurationValueDesc);
        }
    }

    /**
     * Returns the configuration keys for the checkout texts for each installed
     * language (`CHECKOUT_TITLE_EN`, etc.)
     *
     * @return array
     */
    public static function getConfigurationKeys(): array
    {
        $keys             = array();
        $keyCheckoutTitle = Constants::CONFIGURATION_CHECKOUT_TITLE . '_';
        $keyCheckoutDesc  = Constants::CONFIGURATION_CHECKOUT_DESC . '_';

        $languages_query = xtc_db_query(
            sprintf(
                'SELECT *
                   FROM `%s`',
                TABLE_LANGUAGES
            )
        );

        while ($language = xtc_db_fetch_array($languages_query)) {
            $code = strtoupper($language['code']);

            $keys[] = $keyCheckoutTitle . $code;
            $keys[] = $keyCheckoutDesc . $code;
        }

        return $keys;
    }

    /**
     * Returns the configuration value for a specified configuration key.
     *
     * @param string $configurationKey The checkout configuration key.
     *
     * @return string The configuration value for the specified configuration
     *                key.
     */
    public static function getConfigurationValue(string $configurationKey): string
    {
        $configurationKeyLanguageCode = self::getLanguageCode($configurationKey);
        $configurationKeyWithoutCode  = substr($configurationKey, 0, -1 * (strlen($configurationKeyLanguageCode) + 1));

        $configurationValue = '';

        /** Fallback values */
        switch ($configurationKeyWithoutCode) {
            case Constants::CONFIGURATION_CHECKOUT_TITLE:
                $configurationValue = sprintf(
                    'Your purchase at %s',
                    HTTPS_SERVER
                );
                break;

            case Constants::CONFIGURATION_CHECKOUT_DESC:
                $configurationValue = 'We thank you for your trust.';
                break;
        }

        /** Language specific default values */
        switch ($configurationKey) {
            case Constants::CONFIGURATION_CHECKOUT_TITLE . '_DE':
                $configurationValue = sprintf(
                    'Ihr Einkauf bei %s',
                    HTTPS_SERVER
                );
                break;

            case Constants::CONFIGURATION_CHECKOUT_DESC . '_DE':
                $configurationValue = 'Wir bedanken uns für Ihr Vertrauen.';
                break;
        }

        return $configurationValue;
    }
}
