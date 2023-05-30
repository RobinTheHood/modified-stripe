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
 *
 * @phpcs:disable PSR1.Files.SideEffects
 */

use RobinTheHood\Stripe\Classes\Constants;

$prefix = Constants::MODULE_PAYMENT_NAME . '_';

define($prefix . 'TITLE', 'Stripe Zahlungsmodul © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'Ein modified-shop Modul das Zahlungen via Stripe ermöglicht');
define($prefix . 'STATUS_TITLE', 'robinthehood/stripe Modul aktivieren?');
define($prefix . 'STATUS_DESC', '');

// TEXT_TITLE (required) to display the payment name on checkout_confirmation.php, admin/customers_status.php, etc.
define($prefix . 'TEXT_TITLE', 'Stripe (RobinTheHood)');

/**
 * API
 */
define($prefix . 'API_SANDBOX_SECRET_TITLE', 'Geheimschlüssel im Test-Modus');
define($prefix . 'API_SANDBOX_SECRET_DESC', 'Verwenden Sie diesen Schlüssel, um Anfragen auf Ihrem Server im Test-Modus zu authentifizieren. Standardmäßig können Sie diesen Schlüssel verwenden, um jede API-Anfrage ohne Einschränkungen durchzuführen.');
define($prefix . 'API_SANDBOX_KEY_TITLE', 'Veröffentlichbarer Schlüssel im Test-Modus');
define($prefix . 'API_SANDBOX_KEY_DESC', 'Verwenden Sie diesen Schlüssel zu Testzwecken im clientseitigen Code Ihrer Web- oder Mobil-App.');

define($prefix . 'API_LIVE_SECRET_TITLE', 'Geheimschlüssel für den Live-Modus');
define($prefix . 'API_LIVE_SECRET_DESC', 'Verwenden Sie diesen Schlüssel, um Anfragen auf Ihrem Server im Live-Modus zu authentifizieren. Standardmäßig können Sie diesen Schlüssel verwenden, um jede API-Anfrage ohne Einschränkungen durchzuführen.');
define($prefix . 'API_LIVE_KEY_TITLE', 'Veröffentlichbarer Schlüssel im Live-Modus');
define($prefix . 'API_LIVE_KEY_DESC', 'Verwenden Sie diesen Schlüssel im clientseitigen Code Ihrer Web- oder Mobil-App, wenn Sie bereit sind, Ihre App zu starten.');
/** */

/**
 * Checkout
 */
define($prefix . 'CHECKOUT_TITLE_TITLE', 'Checkout Titel');
define($prefix . 'CHECKOUT_TITLE_DESC', 'Text der im Stripe checkout oben erscheint (wie z. B. <i>Einkauf bei demo-shop.de</i>).');
define($prefix . 'CHECKOUT_DESC_TITLE', 'Checkout Beschreibung');
define($prefix . 'CHECKOUT_DESC_DESC', 'Text der im Stripe checkout unten erscheint (wie z. B. <i>Bestellung von Max Mustermann am 01.01.2034</i>)');
/** */
