<tr>
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
