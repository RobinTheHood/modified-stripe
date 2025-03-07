<?php
/**
 * @var RobinTheHood\Stripe\Classes\View\OrderDetailView $view
 */
$paymentIntent = $view->getPaymentIntent();
$charge = $view->getCharge();
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$refundableAmount = $view->getRefundableAmount();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stripe - Rückerstattung</title>
    <meta charset="utf-8">
    <style>
        .rth-stripe-refund-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .rth-stripe-refund-container h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .rth-stripe-refund-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .rth-stripe-refund-info p {
            margin: 5px 0;
        }
        
        .rth-stripe-form-group {
            margin-bottom: 15px;
        }
        
        .rth-stripe-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .rth-stripe-form-group input,
        .rth-stripe-form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .rth-stripe-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .rth-stripe-button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .rth-stripe-button-danger {
            background-color: #ef3b5a;
            color: white;
        }
        
        .rth-stripe-button-secondary {
            background-color: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="rth-stripe-refund-container">
        <h1>Rückerstattung durchführen</h1>
        
        <div class="rth-stripe-refund-info">
            <p><strong>Payment Intent ID:</strong> <?php echo $paymentIntent->id; ?></p>
            <p><strong>Charge ID:</strong> <?php echo $charge->id; ?></p>
            <p><strong>Bestellnummer:</strong> <?php echo $orderId; ?></p>
            <p><strong>Bereits gezahlt:</strong> 
                <?php echo $view->formatAmount($charge->amount, $charge->currency); ?>
            </p>
            <?php
            $refundedAmount = $charge->amount_refunded;
            ?>
            <?php if ($refundedAmount > 0) : ?>
            <p><strong>Bereits rückerstattet:</strong> 
                <?php echo $view->formatAmount($refundedAmount, $charge->currency); ?>
            </p>
            <?php endif; ?>
            <p><strong>Verfügbar für Rückerstattung:</strong> 
                <?php echo $view->formatAmount($refundableAmount * 100, $charge->currency); ?>
            </p>
        </div>
        
        <form method="post" action="rth_stripe.php?action=refund&order_id=<?php echo $orderId; ?>&payment_intent_id=<?php echo $paymentIntent->id; ?>">
            <div class="rth-stripe-form-group">
                <label for="refund_amount">Rückerstattungsbetrag (<?php echo strtoupper($charge->currency); ?>):</label>
                <input type="number" 
                       id="refund_amount" 
                       name="refund_amount" 
                       value="<?php echo $refundableAmount; ?>" 
                       step="0.01" 
                       min="0.01" 
                       max="<?php echo $refundableAmount; ?>" 
                       required>
                <p><small>Maximaler Betrag: <?php echo $view->formatAmount($refundableAmount * 100, $charge->currency); ?></small></p>
            </div>
            
            <div class="rth-stripe-form-group">
                <label for="refund_reason">Grund für die Rückerstattung (optional):</label>
                <select id="refund_reason" name="refund_reason">
                    <option value="">Bitte wählen Sie einen Grund aus...</option>
                    <option value="duplicate">Doppelte Zahlung</option>
                    <option value="fraudulent">Betrügerisch</option>
                    <option value="requested_by_customer">Vom Kunden angefordert</option>
                </select>
            </div>
            
            <div class="rth-stripe-actions">
                <a href="orders.php?oID=<?php echo $orderId; ?>&action=edit" class="rth-stripe-button rth-stripe-button-secondary">Abbrechen</a>
                <button type="submit" class="rth-stripe-button rth-stripe-button-danger">Rückerstattung durchführen</button>
            </div>
        </form>
    </div>
</body>
</html>
