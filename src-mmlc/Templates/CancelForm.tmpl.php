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
    <title>Stripe - Zahlung stornieren</title>
    <meta charset="utf-8">
    <style>
        .rth-stripe-cancel-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        
        .rth-stripe-cancel-container h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .rth-stripe-cancel-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .rth-stripe-cancel-info p {
            margin: 5px 0;
        }
        
        .rth-stripe-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .rth-stripe-form-group {
            margin-bottom: 15px;
        }
        
        .rth-stripe-form-group label {
            display: inline-flex;
            align-items: center;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .rth-stripe-form-group input[type="checkbox"] {
            margin-right: 10px;
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
            background-color: #6c757d;
            color: white;
        }
        
        .rth-stripe-button-secondary {
            background-color: #f0f0f0;
            color: #333;
        }
        
        .rth-stripe-button-danger[disabled] {
            background-color: #b6b6b6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="rth-stripe-cancel-container">
        <h1>Zahlung stornieren</h1>
        
        <div class="rth-stripe-cancel-info">
            <p><strong>Payment Intent ID:</strong> <?php echo $paymentIntent->id; ?></p>
            <p><strong>Bestellnummer:</strong> <?php echo $orderId; ?></p>
            <p><strong>Status:</strong> <?php echo $paymentIntent->status; ?></p>
            <p><strong>Betrag:</strong> 
                <?php echo $view->formatAmount($paymentIntent->amount, $paymentIntent->currency); ?>
            </p>
        </div>
        
        <div class="rth-stripe-warning">
            <strong>Warnung:</strong> Durch das Stornieren dieser Zahlung wird die Autorisierung aufgehoben und die Zahlung kann nicht mehr eingezogen werden. Diese Aktion kann nicht rückgängig gemacht werden.
        </div>
        
        <form method="post" action="rth_stripe.php?action=cancel&order_id=<?php echo $orderId; ?>&payment_intent_id=<?php echo $paymentIntent->id; ?>" id="cancelForm">
            <div class="rth-stripe-form-group">
                <label>
                    <input type="checkbox" id="confirm_checkbox" required>
                    Ich bestätige, dass ich die Zahlung stornieren möchte
                </label>
            </div>
            
            <input type="hidden" name="confirm_cancel" value="yes">
            
            <div class="rth-stripe-actions">
                <a href="orders.php?oID=<?php echo $orderId; ?>&action=edit" class="rth-stripe-button rth-stripe-button-secondary">Abbrechen</a>
                <button type="submit" id="cancelButton" class="rth-stripe-button rth-stripe-button-danger" disabled>Zahlung stornieren</button>
            </div>
        </form>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkbox = document.getElementById('confirm_checkbox');
                const button = document.getElementById('cancelButton');
                
                checkbox.addEventListener('change', function() {
                    button.disabled = !this.checked;
                });
            });
        </script>
    </div>
</body>
</html>
