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

define($prefix . 'ALLOWED_TITLE', 'Erlaubte Länder');
define($prefix . 'ALLOWED_DESC', 'Kommagetrennte ISO-Ländercodes (z. B. AT,DE). Leer lassen, um alle Länder zu erlauben.');
define($prefix . 'ZONE_TITLE', 'Zahlungszone');
define($prefix . 'ZONE_DESC', 'Wenn eine Zone ausgewählt ist, steht Stripe nur Kunden zur Verfügung, deren Rechnungsadresse in der ausgewählten Steuerzone liegt.');

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
define($prefix . 'API_LIVE_ENDPOINT_SECRET_DESC', 'Dieser Schlüssel wird benötigt, damit der Server kontrollieren kann, ob die Anfragen von Stripe kommen. Mehr Informationen hierzu sind in der Installationsanleitung zum Modul enthalten. Sie benötigen folgende Daten, um einen Webhook bei Stripe einzurichten: <br>Webhook-Endpoint: <code style="color: rgb(98, 90, 250)">' . $webhookEndpoint . '</code><br>Ereignisse: <code style="color: rgb(98, 90, 250)">checkout.session.completed</code>, <code style="color: rgb(98, 90, 250)">checkout.session.expired</code>, <code style="color: rgb(98, 90, 250)">payment_intent.amount_capturable_updated</code> und <code style="color: rgb(98, 90, 250)">charge.succeeded</code>');

define($prefix . 'CHECKOUT_TITLE_TITLE', 'Checkout Titel');
define($prefix . 'CHECKOUT_TITLE_DESC', 'Text, der im Stripe Checkout als Titel verwendet werden soll.');
define($prefix . 'CHECKOUT_DESC_TITLE', 'Checkout Beschreibung');
define($prefix . 'CHECKOUT_DESC_DESC', 'Text, der im Stripe Checkout als Beschreibung verwendet werden soll.');

define($prefix . 'PAYMENT_TITLE_TITLE', 'Zahlungsname');
define($prefix . 'PAYMENT_TITLE_DESC', 'Name, der im Checkout Payment Schritt angezeigt wird.');
define($prefix . 'PAYMENT_DESC_TITLE', 'Zahlungsbeschreibung');
define($prefix . 'PAYMENT_DESC_DESC', 'Beschreibung, die den Kunden beim Checkout Payment Schritt angezeigt wird.');

define($prefix . 'ICON_URL_TITLE', 'Icon URL');
define($prefix . 'ICON_URL_DESC', 'URL zum Icon, das anstelle der Beschreibung angezeigt werden soll (z.B. DE::https://example.com/stripe-icon-de.png||EN::https://example.com/stripe-icon-en.png). Wenn leer, wird die normale Zahlungsbeschreibung angezeigt.');

define($prefix . 'ORDER_STATUS_PENDING_TITLE', 'Bestellstatus für ausstehende Bezahlung');
define($prefix . 'ORDER_STATUS_PENDING_DESC', 'Einige Zahlungsarten werden von Stripe zeitversetzt als bezahlt erfasst. Welchen Bestellstatus soll die Bestellung in der Zwischenzeit erhalten?');

define($prefix . 'ORDER_STATUS_PAID_TITLE', 'Bestellstatus für erfolgreiche Bezahlung');
define($prefix . 'ORDER_STATUS_PAID_DESC', 'Welchen Bestellstatus soll die Bestellung erhalten, nachdem Stripe die Zahlung als erfolgreich geprüft hat?');

define($prefix . 'ORDER_STATUS_AUTHORIZED_TITLE', 'Bestellstatus für autorisierte Zahlung');
define($prefix . 'ORDER_STATUS_AUTHORIZED_DESC', 'Welchen Bestellstatus soll die Bestellung erhalten, wenn eine Zahlung autorisiert, aber noch nicht eingezogen wurde?');

define($prefix . 'ORDER_STATUS_CAPTURED_TITLE', 'Bestellstatus nach Capture');
define($prefix . 'ORDER_STATUS_CAPTURED_DESC', 'Welchen Bestellstatus soll die Bestellung erhalten, nachdem eine autorisierte Zahlung erfolgreich eingezogen wurde?');

define($prefix . 'ORDER_STATUS_CANCELED_TITLE', 'Bestellstatus nach Storno');
define($prefix . 'ORDER_STATUS_CANCELED_DESC', 'Welchen Bestellstatus soll die Bestellung erhalten, wenn eine Zahlung storniert wurde?');

define($prefix . 'ORDER_STATUS_REFUNDED_TITLE', 'Bestellstatus nach Rückzahlung');
define($prefix . 'ORDER_STATUS_REFUNDED_DESC', 'Welchen Bestellstatus soll die Bestellung erhalten, wenn eine Zahlung zurückerstattet wurde?');

define($prefix . 'MANUAL_CAPTURE_TITLE', 'Manuelles Capture');
define($prefix . 'MANUAL_CAPTURE_DESC', 'Manuelles Erfassen der Zahlungen aktivieren? Wenn ja, werden Zahlungsbeträge autorisiert, aber nicht automatisch eingezogen. Sie müssen Zahlungen manuell in der Bestellung oder von Ihrem Stripe-Dashboard aus erfassen.');

define($prefix . 'RESET_AUTO_INCREMENT_AFTER_TEMP_DELETE_TITLE', 'Lücken in Bestellnummern vermeiden?');
define($prefix . 'RESET_AUTO_INCREMENT_AFTER_TEMP_DELETE_DESC', 'Wenn ja, wird nach dem Löschen von temporären Bestellungen der Auto-Inkrement-Wert zurückgesetzt, um Lücken in den Bestellnummern zu vermeiden.');

define($prefix . 'PAYOUT_NOTIFY_ENABLE_TITLE', 'Payout Benachrichtigungen aktivieren?');
define($prefix . 'PAYOUT_NOTIFY_ENABLE_DESC', 'Wenn aktiviert, wird bei neuen Stripe Auszahlungen automatisch eine Übersicht per E-Mail versendet.');
define($prefix . 'PAYOUT_NOTIFY_RECIPIENTS_TITLE', 'Payout E-Mail Empfänger');
define($prefix . 'PAYOUT_NOTIFY_RECIPIENTS_DESC', 'Kommagetrennte Liste zusätzlicher Empfänger. Leer lassen, um nur die Shop-Betreiber E-Mail zu verwenden.');
define($prefix . 'SECURE_ACTION_TOKEN_TITLE', 'Sicheres Aktions-Token');
define($prefix . 'SECURE_ACTION_TOKEN_DESC', 'Optional: Wenn gesetzt, müssen geschützte öffentliche Aufrufe (Cron/Tools) mit ?token=TOKEN erfolgen. Leer lassen, um die Token-Prüfung zu deaktivieren.');
