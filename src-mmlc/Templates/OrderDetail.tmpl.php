<style>
    #payment_block table:has(> tbody > tr > td > div.rth-stripe:first-child) {
        width: 100%;
    }

    .rth-stripe {
        padding: 20px;
        background-color: white;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .rth-stripe .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .rth-stripe h3 {
        color: rgb(26, 27, 37);
        font-size: 18px;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    
    .rth-stripe .stripe-dashboard-btn {
        background-color: #635bff;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    
    .rth-stripe .stripe-dashboard-btn:hover {
        background-color: #4b45c6;
    }

    .rth-stripe .stripe-dashboard-btn .icon {
        margin-right: 5px;
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
        <div class="rth-stripe"">
            <div class="header-container">
                <h3>Stripe - Zahlungsinformationen</h3>
                <?php if (!empty($stripePaymentIntentId)) : ?>
                <a href="https://dashboard.stripe.com/payments/<?php echo $stripePaymentIntentId; ?>" target="_blank" class="stripe-dashboard-btn">
                    <span class="icon">🔗</span> Stripe Dashboard
                </a>
                <?php endif; ?>
            </div>
            <div id="rth-stripe-container">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                </div>
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
                return response.text().then(text => {
                    throw new Error(`Network response was not ok: ${text}`);
                });
            }
            return response.text();
        })
        .then(data => {
            if (!data || data.trim() === '') {
                throw new Error('Keine Daten verfügbar');
            }
            stripeContainer.innerHTML = data;
        })
        .catch(error => {
            stripeContainer.innerHTML = `
                <div class="error-message">
                    <strong>Fehler beim Laden der Zahlungsinformationen</strong><br>
                    Die Stripe-Daten konnten nicht geladen werden. Bitte versuchen Sie es später erneut.
                    <br>
                    <small> (Fehler: ${error.message})</small>
                </div>
            `;
            console.error('Error fetching payment info:', error);
        });
    });
</script>
