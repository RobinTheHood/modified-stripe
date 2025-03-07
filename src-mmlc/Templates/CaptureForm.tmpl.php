<?php
/**
 * @var RobinTheHood\Stripe\Classes\View\OrderDetailView $view
 */
$paymentIntent = $view->getPaymentIntent();
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stripe - Zahlung Einnehmen</title>
    <meta charset="utf-8">
    <style>
        .rth-stripe-capture-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .rth-stripe-capture-container h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .rth-stripe-capture-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .rth-stripe-capture-info p {
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
        
        .rth-stripe-form-group input {
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
        
        .rth-stripe-button-primary {
            background-color: #635bff;
            color: white;
        }
        
        .rth-stripe-button-secondary {
            background-color: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="rth-stripe-capture-container">
        <h1>Zahlung Einnehmen (Capture)</h1>
        
        <div class="rth-stripe-capture-info">
            <p><strong>Payment Intent ID:</strong> <?php echo $paymentIntent->id; ?></p>
            <p><strong>Bestellnummer:</strong> <?php echo $orderId; ?></p>
            <p><strong>Autorisierter Betrag:</strong> 
                <?php echo $view->formatAmount($paymentIntent->amount, $paymentIntent->currency); ?>
            </p>
            <?php
            $captureDeadline = $view->calculateCaptureDeadline();
            $remainingTime = $view->getRemainingTimeText($captureDeadline);
            ?>
            <p><strong>Verbleibende Zeit zur Einnahme:</strong> <?php echo $remainingTime; ?></p>
        </div>
        
        <form method="post" action="rth_stripe.php?action=capture&order_id=<?php echo $orderId; ?>&payment_intent_id=<?php echo $paymentIntent->id; ?>">
            <div class="rth-stripe-form-group">
                <label for="capture_amount">Einzunehmender Betrag (<?php echo strtoupper($paymentIntent->currency); ?>):</label>
                <input type="number" 
                       id="capture_amount" 
                       name="capture_amount" 
                       value="<?php echo $paymentIntent->amount / 100; ?>" 
                       step="0.01" 
                       min="0.01" 
                       max="<?php echo $paymentIntent->amount / 100; ?>" 
                       required>
                <p><small>Maximaler Betrag: <?php echo $view->formatAmount($paymentIntent->amount, $paymentIntent->currency); ?></small></p>
            </div>
            
            <div class="rth-stripe-actions">
                <a href="orders.php?oID=<?php echo $orderId; ?>&action=edit" class="rth-stripe-button rth-stripe-button-secondary">Abbrechen</a>
                <button type="submit" class="rth-stripe-button rth-stripe-button-primary">Zahlung jetzt einnehmen</button>
            </div>
        </form>
    </div>
</body>
</html>
