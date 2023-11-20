<?php

if (rth_is_module_disabled(payment_rth_stripe::NAME)) {
    return;
}

if (payment_rth_stripe::class !== $order->info['payment_method']) {
    return;
}

?>

<style>
    .rth-stripe {
        padding: 20px;
        background-color: white;
        margin-top: 10px;
        width: 100%
    }

    .rth-stripe h3 {
        color: rgb(26, 27, 37);
        font-size: 18px;
        margin-top: 0px;
    }

    .rth-stripe .rth-stripe-content {
        border-top: 1px solid rgb(235,238,241);
        padding-top: 20px;
    }

    .rth-stripe .rth-stripe-property-list-row {
        display: flex;
        margin: 8px 0px;
    }

    .rth-stripe .rth-stripe-property-list-item-label {
        min-width: 180px;
        color: rgb(104, 115, 133);
    }

    .rth-stripe .rth-stripe-property-list-item-value {
        color: rgb(65, 69, 82);
    }
</style>

<tr>
    <td colspan="2">
        <div class="rth-stripe">
            <h3>Stripe - Zahlungsmethode</h3>
            <div class="rth-stripe-content">
                <div class="rth-stripe-property-list">
                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">ID</div>
                        <div class="rth-stripe-property-list-item-value">pm_1O5ngnJIsfvAtVBdxcIxeu3b</div>
                    </div>

                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Nummer</div>
                        <div class="rth-stripe-property-list-item-value">•••• 4242</div>
                    </div>

                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Fingerabdruck</div>
                        <div class="rth-stripe-property-list-item-value">uyMGel009AKHV6UG</div>
                    </div>

                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Gültig bis</div>
                        <div class="rth-stripe-property-list-item-value">04 / 2024</div>
                    </div>

                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Typ</div>
                        <div class="rth-stripe-property-list-item-value">Visa credit Karte</div>
                    </div>

                    <div class="rth-stripe-property-list-row">
                        <div class="rth-stripe-property-list-item-label">Aussteller</div>
                        <div class="rth-stripe-property-list-item-value">Stripe Payments UK Limited</div>
                    </div>
                </div>
            </div>
        </div>
    </td>
</td>

<tr style="display: none">
    <td>
        <div style="border: 1px solid rgb(153, 102, 255);">
            Stripe Payment Info
            <table>
                <tr>
                    <th>Zahlungsdetails</th>
                    <td>
                        <ul>
                            <li><strong>Karteninhaber:</strong> Max Mustermann</li>
                            <li><strong>Kartennummer:</strong> **** **** **** 1234</li>
                            <li><strong>Ablaufdatum:</strong> 12/24</li>
                            <li><strong>Zahlungsart:</strong> Kreditkarte</li>
                            <li><strong>Transaktions-ID:</strong> XXXXXXXXXXXXXX</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>Transaktionen</th>
                    <td>
                        <ul>
                            <li>
                            <strong>Datum:</strong> 28.05.2023 17:06:29
                            <ul>
                                <li><strong>Status:</strong> Abgeschlossen</li>
                                <li><strong>Betrag:</strong> 41,90 EUR</li>
                                <li><strong>Gebühr:</strong> 1,39 EUR</li>
                                <li><strong>ID:</strong> XXXXXXXXXXXXXX</li>
                            </ul>
                            </li>
                            <li>
                            <strong>Datum:</strong> 27.05.2023 10:12:45
                            <ul>
                                <li><strong>Status:</strong> Abgelehnt</li>
                                <li><strong>Betrag:</strong> 15,00 EUR</li>
                                <li><strong>Gebühr:</strong> 0,75 EUR</li>
                                <li><strong>ID:</strong> XXXXXXXXXXXXXX</li>
                            </ul>
                            </li>
                            <!-- Weitere Transaktionen -->
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>Rückzahlung</th>
                    <td>
                        <form>
                            <label for="refund_amount">Betrag:</label>
                            <input type="text" name="refund_amount" id="refund_amount" placeholder="Betrag eingeben">
                            <br>
                            <label for="refund_comment">Kommentar:</label>
                            <textarea name="refund_comment" id="refund_comment" placeholder="Kommentar eingeben"></textarea>
                            <br>
                            <input type="submit" value="Rückzahlung durchführen">
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </td>
</tr>
