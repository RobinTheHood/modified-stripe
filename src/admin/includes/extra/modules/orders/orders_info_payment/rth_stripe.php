<?php

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

if (payment_rth_stripe::class !== $order->info['payment_method']) {
    return;
}

// Get the order ID from the URL parameter
$orderId = isset($_GET['oID']) ? (int)$_GET['oID'] : 0;

// Retrieve payment intent ID from order
$paymentIntentId = 'pi_3QzP3oJIsfvAtVBd0226XgkJ'; // This should be retrieved from your order data
?>

<style>
    .rth-stripe {
        padding: 20px;
        background-color: white;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .rth-stripe h3 {
        color: rgb(26, 27, 37);
        font-size: 18px;
        margin-top: 0px;
        margin-bottom: 15px;
    }

    .rth-stripe .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px;
    }

    .rth-stripe .loading-spinner .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border-left-color: #635bff;
        animation: spin 1s linear infinite;
    }

    .rth-stripe .error-message {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
        text-align: center;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<tr>
    <td colspan="2">
        <div class="rth-stripe" id="rth-stripe-container">
            <h3>Stripe - Zahlungsinformationen</h3>
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
        </div>
    </td>
</tr>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stripeContainer = document.getElementById('rth-stripe-container');
    
    // AJAX request to fetch payment information
    fetch('rth_stripe.php?action=getStripePaymentDetails&order_id=<?php echo $orderId; ?>')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            stripeContainer.innerHTML = data;
        })
        .catch(error => {
            stripeContainer.innerHTML = `
                <h3>Stripe - Zahlungsinformationen</h3>
                <div class="error-message">
                    <strong>Fehler beim Laden der Zahlungsinformationen</strong><br>
                    Die Stripe-Daten konnten nicht geladen werden. Bitte versuchen Sie es später erneut.
                </div>
            `;
            console.error('Error fetching payment info:', error);
        });
});
</script>
