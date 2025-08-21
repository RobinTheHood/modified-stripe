<?php

/**
 * German language file for Stripe temporary order messages
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

return [
    'title' => 'Temporäre Bestellung',
    'description' => 'Diese Bestellung ist noch nicht mit einer Stripe-Zahlung verknüpft. Dies ist ein normaler Zustand für Bestellungen, die gerade erst erstellt wurden.',
    'what_means' => 'Was bedeutet das?',
    'customer_checkout' => 'Der Kunde befindet sich noch im Stripe-Checkout-Prozess',
    'payment_not_completed' => 'Die Zahlung wurde noch nicht abgeschlossen oder bestätigt',
    'auto_deleted' => 'Diese Bestellung wird automatisch gelöscht, wenn:',
    'customer_cancels' => 'Der Kunde die Zahlung abbricht',
    'session_expires' => 'Die Stripe-Checkout-Session abläuft (normalerweise nach 24 Stunden)',
    'payment_error' => 'Ein Fehler im Zahlungsprozess auftritt',
    'what_to_do' => 'Was müssen Sie tun?',
    'no_action_needed' => 'Normalerweise ist keine Aktion erforderlich. Wenn der Kunde die Zahlung erfolgreich abschließt, wird diese Bestellung automatisch mit den Stripe-Zahlungsdetails verknüpft und der Status entsprechend aktualisiert. Sollte dies nicht der Fall sein, überprüfen Sie die Bestellung und stornieren Sie sie gegebenenfalls.',
];
