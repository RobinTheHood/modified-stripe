<?php

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
