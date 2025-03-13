<?php
// This file contains just the HTML template for the payment info display
// It expects $paymentIntent and other variables to be set by the calling script
/**
 * @var RobinTheHood\Stripe\Classes\View\OrderDetailView $view
 */
$paymentIntent = $view->getPaymentIntent();
?>
<style>
    .rth-stripe-content {
        border-top: 1px solid rgb(235, 238, 241);
        padding-top: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
    }

    .rth-stripe .rth-stripe-column {
        flex: 1;
        min-width: 300px;
    }

    .rth-stripe .rth-stripe-section {
        margin-bottom: 25px;
    }

    .rth-stripe .rth-stripe-section-title {
        font-weight: 600;
        font-size: 15px;
        color: #333;
        margin-bottom: 12px;
        padding-bottom: 5px;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Full-width actions at the bottom */
    .rth-stripe .rth-stripe-actions {
        width: 100%;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .rth-stripe .rth-stripe-content {
            flex-direction: column;
        }

        .rth-stripe .rth-stripe-column {
            width: 100%;
        }
    }

    .rth-stripe .rth-stripe-property-list-row {
        display: flex;
        margin: 8px 0px;
    }

    .rth-stripe .rth-stripe-property-list-item-label {
        min-width: 180px;
        color: rgb(104, 115, 133);
        font-weight: 500;
    }

    .rth-stripe .rth-stripe-property-list-item-value {
        color: rgb(65, 69, 82);
    }

    .rth-stripe .rth-stripe-button {
        background-color: #635bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
    }

    .rth-stripe .rth-stripe-button:hover {
        background-color: #524deb;
    }

    .rth-stripe .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .rth-stripe .status-badge.success {
        background-color: #d4edda;
        color: #155724;
    }

    .rth-stripe .status-badge.warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .rth-stripe .status-badge.danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .rth-stripe .status-badge.info {
        background-color: #cce5ff;
        color: #004085;
    }

    .rth-stripe .status-badge.secondary {
        background-color: #e2e3e5;
        color: #383d41;
    }
</style>
<div class="rth-stripe-content">
    <!-- Left Column -->
    <div class="rth-stripe-column">
        <!-- Payment Intent Basic Details -->
        <div class="rth-stripe-section">
            <div class="rth-stripe-section-title">Zahlungsübersicht</div>
            <div class="rth-stripe-property-list">
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Payment Intent ID</div>
                    <div class="rth-stripe-property-list-item-value"><?= $paymentIntent->id; ?></div>
                </div>
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Status</div>
                    <div class="rth-stripe-property-list-item-value">
                        <span class="status-badge <?= $view->getStatusBadgeClass($paymentIntent->status); ?>">
                            <?= $paymentIntent->status; ?>
                        </span>
                    </div>
                </div>
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Betrag</div>
                    <div class="rth-stripe-property-list-item-value">
                        <?= $view->formatAmount($paymentIntent->amount, $paymentIntent->currency); ?>
                    </div>
                </div>
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Währung</div>
                    <div class="rth-stripe-property-list-item-value"><?= strtoupper($paymentIntent->currency); ?></div>
                </div>
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Erstellt am</div>
                    <div class="rth-stripe-property-list-item-value"><?= $view->formatTimestamp($paymentIntent->created); ?></div>
                </div>
                <?php if ($paymentIntent->canceled_at) : ?>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Storniert am</div>
                        <div class="rth-stripe-property-list-item-value"><?= $view->formatTimestamp($paymentIntent->canceled_at); ?></div>
                    </div>
                <?php endif; ?>
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Beschreibung</div>
                    <div class="rth-stripe-property-list-item-value">
                        <?= $paymentIntent->description ?: 'Keine Beschreibung'; ?>
                    </div>
                </div>
                <div class="rth-stripe-property-list-row">
                    <div class="rth-stripe-property-list-item-label">Capture-Methode</div>
                    <div class="rth-stripe-property-list-item-value">
                        <?= $paymentIntent->capture_method; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charges and Refunds -->
        <?php if (isset($paymentIntent->latest_charge) && $paymentIntent->latest_charge) : ?>
            <div class="rth-stripe-section">
                <div class="rth-stripe-section-title">Zahlungsvorgänge</div>
                <div class="rth-stripe-property-list">
                    <?php
                    $charge = $view->getCharge();
                    ?>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Charge ID</div>
                        <div class="rth-stripe-property-list-item-value"><?= $charge->id; ?></div>
                    </div>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Status</div>
                        <div class="rth-stripe-property-list-item-value">
                            <span class="status-badge <?= $view->getStatusBadgeClass($charge->status); ?>">
                                <?= $charge->status; ?>
                            </span>
                        </div>
                    </div>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Betrag</div>
                        <div class="rth-stripe-property-list-item-value">
                            <?= $view->formatAmount($charge->amount, $charge->currency); ?>
                        </div>
                    </div>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Gebühren</div>
                        <div class="rth-stripe-property-list-item-value">
                            <?php
                            echo isset($charge->balance_transaction) && $charge->balance_transaction ?
                                $view->formatAmount($charge->balance_transaction->fee, $charge->currency) :
                                'Keine Angabe';
                            ?>
                        </div>
                    </div>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Erstellt am</div>
                        <div class="rth-stripe-property-list-item-value"><?= $view->formatTimestamp($charge->created); ?></div>
                    </div>

                    <?php if ($charge->captured) : ?>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">Eingenommen am</div>
                            <div class="rth-stripe-property-list-item-value">
                                <?= $view->formatTimestamp($charge->captured); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($charge->refunds->data)) : ?>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">Rückzahlungen</div>
                            <div class="rth-stripe-property-list-item-value">
                                <span class="status-badge info">
                                    <?= count($charge->refunds->data); ?> Rückzahlung(en)
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($charge->refunds->data)) : ?>
                <div class="rth-stripe-section">
                    <div class="rth-stripe-section-title">Rückzahlungen</div>
                    <?php foreach ($charge->refunds->data as $refund) : ?>
                        <div class="rth-stripe-property-list" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #f0f0f0;">
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">Refund ID</div>
                                <div class="rth-stripe-property-list-item-value"><?= $refund->id; ?></div>
                            </div>
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">Betrag</div>
                                <div class="rth-stripe-property-list-item-value">
                                    <?= $view->formatAmount($refund->amount, $refund->currency); ?>
                                </div>
                            </div>
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">Status</div>
                                <div class="rth-stripe-property-list-item-value">
                                    <span class="status-badge <?= $view->getStatusBadgeClass($refund->status); ?>">
                                        <?= $refund->status; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">Datum</div>
                                <div class="rth-stripe-property-list-item-value">
                                    <?= $view->formatTimestamp($refund->created); ?>
                                </div>
                            </div>
                            <?php if (!empty($refund->reason)) : ?>
                                <div class="rth-stripe-property-list-row">
                                    <div class="rth-stripe-property-list-item-label">Grund</div>
                                    <div class="rth-stripe-property-list-item-value"><?= $refund->reason; ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div class="rth-stripe-column">
        <!-- Payment Method Details -->
        <?php if ($paymentIntent->payment_method) : ?>
            <div class="rth-stripe-section">
                <div class="rth-stripe-section-title">Zahlungsmethode</div>
                <div class="rth-stripe-property-list">
                    <?php
                    $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentIntent->payment_method);
                    if ('card' === $paymentMethod->type && isset($paymentMethod->card)) :
                        ?>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">ID</div>
                            <div class="rth-stripe-property-list-item-value"><?= $paymentMethod->id; ?></div>
                        </div>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">Kartennummer</div>
                            <div class="rth-stripe-property-list-item-value">•••• <?= $paymentMethod->card->last4; ?></div>
                        </div>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">Gültig bis</div>
                            <div class="rth-stripe-property-list-item-value">
                                <?= $paymentMethod->card->exp_month; ?> / <?= $paymentMethod->card->exp_year; ?>
                            </div>
                        </div>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">Kartenmarke</div>
                            <div class="rth-stripe-property-list-item-value"><?= ucfirst($paymentMethod->card->brand); ?></div>
                        </div>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">Fingerabdruck</div>
                            <div class="rth-stripe-property-list-item-value"><?= $paymentMethod->card->fingerprint; ?></div>
                        </div>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label">3D Secure</div>
                            <div class="rth-stripe-property-list-item-value">
                                <?= isset($paymentMethod->card->three_d_secure) ? 'Ja' : 'Nein'; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Billing Details -->
        <?php if ($paymentIntent->customer) : ?>
            <div class="rth-stripe-section">
                <div class="rth-stripe-section-title">Kundendaten</div>
                <div class="rth-stripe-property-list">
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Kunden-ID</div>
                        <div class="rth-stripe-property-list-item-value"><?= $paymentIntent->customer; ?></div>
                    </div>

                    <?php
                    if (!empty($paymentMethod->billing_details)) :
                        $billing = $paymentMethod->billing_details;
                        ?>
                        <?php if (!empty($billing->name)) : ?>
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">Name</div>
                                <div class="rth-stripe-property-list-item-value"><?= $billing->name; ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($billing->email)) : ?>
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">E-Mail</div>
                                <div class="rth-stripe-property-list-item-value"><?= $billing->email; ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($billing->phone)) : ?>
                            <div class="rth-stripe-property-list-row">
                                <div class="rth-stripe-property-list-item-label">Telefon</div>
                                <div class="rth-stripe-property-list-item-value"><?= $billing->phone; ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Capture Deadline (if applicable) -->
        <?php if ('requires_capture' === $paymentIntent->status) : ?>
            <?php
            $charge = $view->getCharge();
            $captureDeadline = $view->calculateCaptureDeadline();
            $remainingTime = $view->getRemainingTimeText($captureDeadline);
            ?>
            <div class="rth-stripe-section">
                <div class="rth-stripe-section-title">Capture-Zeitfenster</div>
                <div class="rth-stripe-property-list">
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Capture-Frist</div>
                        <div class="rth-stripe-property-list-item-value"><?= $view->formatTimestamp($captureDeadline); ?></div>
                    </div>
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Verbleibende Zeit</div>
                        <div class="rth-stripe-property-list-item-value">
                            <span class="status-badge <?= time() > $captureDeadline ? 'danger' : 'warning'; ?>">
                                <?= $remainingTime; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Metadata -->
        <?php if (!empty($paymentIntent->metadata->toArray())) : ?>
            <div class="rth-stripe-section">
                <div class="rth-stripe-section-title">Metadaten</div>
                <div class="rth-stripe-property-list">
                    <?php foreach ($paymentIntent->metadata->toArray() as $key => $value) : ?>
                        <div class="rth-stripe-property-list-row">
                            <div class="rth-stripe-property-list-item-label"><?= $key; ?></div>
                            <div class="rth-stripe-property-list-item-value"><?= $value; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions - Full Width -->
    <div class="rth-stripe-actions">
        <?php if ('requires_capture' === $paymentIntent->status) : ?>
            <a href="rth_stripe.php?action=capture&order_id=<?= $orderId ?>&payment_intent_id=<?= $paymentIntent->id; ?>" class="rth-stripe-button">
                Zahlung Einnehmen (Capture)
            </a>
        <?php endif; ?>

        <?php if (in_array($paymentIntent->status, ['succeeded', 'processing']) && (!$paymentIntent->latest_charge || !$charge->refunded)) : ?>
            <a href="rth_stripe.php?action=refund&order_id=<?= $orderId ?>&payment_intent_id=<?= $paymentIntent->id; ?>" class="rth-stripe-button" style="background-color: #ef3b5a;">
                Rückerstattung durchführen
            </a>
        <?php endif; ?>

        <?php if ('canceled' !== $paymentIntent->status && !in_array($paymentIntent->status, ['succeeded', 'processing'])) : ?>
            <a href="rth_stripe.php?action=cancel&order_id=<?= $orderId ?>&payment_intent_id=<?= $paymentIntent->id; ?>" class="rth-stripe-button" style="background-color: #6c757d;">
                Zahlung stornieren
            </a>
        <?php endif; ?>
    </div>
</div>