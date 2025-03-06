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

$webhookEndpoint = HTTPS_SERVER . DIR_WS_CATALOG . 'rth_stripe.php?action=receiveHook';
$prefix          = 'MODULE_PAYMENT_PAYMENT_RTH_STRIPE_';

define($prefix . 'TITLE', 'Stripe Zahlungsmodul © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'Ein modified-shop Modul das Zahlungen via Stripe ermöglicht');
define($prefix . 'STATUS_TITLE', 'Strip Zahlungsmodul aktivieren?');
define($prefix . 'STATUS_DESC', 'Möchten Sie Zahlungen über Stripe aktivieren?');

// TEXT_TITLE (required) to display the payment name on checkout_confirmation.php, admin/customers_status.php, admin/orders.php etc.
define($prefix . 'TEXT_TITLE', 'Stripe');

define($prefix . 'LIVE_MODE_TITLE', 'Live-Modus aktivieren?');
define($prefix . 'LIVE_MODE_DESC', 'Soll Stripe im Live-Modus arbeiten? Bei nein arbeitet das Modul im Sandbox Test-Modus.');

/**
 * API
 */
define($prefix . 'API_SANDBOX_SECRET_TITLE', 'Geheimschlüssel im Test-Modus');
define($prefix . 'API_SANDBOX_SECRET_DESC', 'Verwenden Sie diesen Schlüssel, um Anfragen auf Ihrem Server im Test-Modus zu authentifizieren. Standardmäßig können Sie diesen Schlüssel verwenden, um jede API-Anfrage ohne Einschränkungen durchzuführen. Mehr Informationen hierzu sind in der Installationsanleitung zum Modul enthalten.');
define($prefix . 'API_SANDBOX_KEY_TITLE', 'Veröffentlichbarer Schlüssel im Test-Modus');
define($prefix . 'API_SANDBOX_KEY_DESC', 'Verwenden Sie diesen Schlüssel zu Testzwecken im clientseitigen Code Ihrer Web- oder Mobil-App. Mehr Informationen hierzu sind in der Installationsanleitung zum Modul enthalten.');
define($prefix . 'API_LIVE_SECRET_TITLE', 'Geheimschlüssel für den Live-Modus');
define($prefix . 'API_LIVE_SECRET_DESC', 'Verwenden Sie diesen Schlüssel, um Anfragen auf Ihrem Server im Live-Modus zu authentifizieren. Standardmäßig können Sie diesen Schlüssel verwenden, um jede API-Anfrage ohne Einschränkungen durchzuführen. Mehr Informationen hierzu sind in der Installationsanleitung zum Modul enthalten.');
define($prefix . 'API_LIVE_KEY_TITLE', 'Veröffentlichbarer Schlüssel im Live-Modus');
define($prefix . 'API_LIVE_KEY_DESC', 'Verwenden Sie diesen Schlüssel im clientseitigen Code Ihrer Web- oder Mobil-App, wenn Sie bereit sind, Ihre App zu starten. Mehr Informationen hierzu sind in der Installationsanleitung zum Modul enthalten.');
define($prefix . 'API_LIVE_ENDPOINT_SECRET_TITLE', 'Geheimer Webhook Schlüssel');
define($prefix . 'API_LIVE_ENDPOINT_SECRET_DESC', 'Dieser Schlüssel wird benötigt, damit der Server kontrollieren kann, ob die Anfragen von Stripe kommen. Mehr Informationen hierzu sind in der Installationsanleitung zum Modul enthalten. Sie benötigen folgende Daten, um einen Webhook bei Stripe einzurichten: <br>Webhook-Endpoint: <code style="color: rgb(98, 90, 250)">' . $webhookEndpoint . '</code><br>Ereignisse: <code style="color: rgb(98, 90, 250)">checkout.session.completed</code>, <code style="color: rgb(98, 90, 250)">checkout.session.expired</code> und <code style="color: rgb(98, 90, 250)">charge.succeeded</code>');

define($prefix . 'CHECKOUT_TITLE_TITLE', 'Checkout Titel');
define($prefix . 'CHECKOUT_TITLE_DESC', 'Text, der im Stripe Checkout als Titel verwendet werden soll.');
define($prefix . 'CHECKOUT_DESC_TITLE', 'Checkout Beschreibung');
define($prefix . 'CHECKOUT_DESC_DESC', 'Text, der im Stripe Checkout als Beschreibung verwendet werden soll.');

define($prefix . 'PAYMENT_TITLE_TITLE', 'Zahlungsname');
define($prefix . 'PAYMENT_TITLE_DESC', 'Name, der im Checkout Payment Schritt angezeigt wird.');
define($prefix . 'PAYMENT_DESC_TITLE', 'Zahlungsbeschreibung');
define($prefix . 'PAYMENT_DESC_DESC', 'Beschreibung, die den Kunden beim Checkout Payment Schritt angezeigt wird.');

define($prefix . 'ORDER_STATUS_PENDING_TITLE', 'Bestellstatus für ausstehende Bezahlung');
define($prefix . 'ORDER_STATUS_PENDING_DESC', 'Einige Zahlungsarten werden von Stripe zeitversetzt als bezahlt erfasst. Welchen Bestellstatus soll die Bestellung in der Zwischenzeit erhalten?');

define($prefix . 'ORDER_STATUS_PAID_TITLE', 'Bestellstatus für erfolgreiche Bezahlung');
define($prefix . 'ORDER_STATUS_PAID_DESC', 'Welchen Bestellstatus soll die Bestellung erhalten, nachdem Stripe die Zahlung als erfolgreich geprüft hat?');
define($prefix . 'MANUAL_CAPTURE_TITLE', 'Manuelles Capture');
define($prefix . 'MANUAL_CAPTURE_DESC', 'Manuelles Erfassen der Zahlungen aktivieren? Wenn ja, werden Zahlungsbeträge autorisiert, aber nicht automatisch eingezogen. Sie müssen Zahlungen manuell in der Bestellung oder von Ihrem Stripe-Dashboard aus erfassen.');
