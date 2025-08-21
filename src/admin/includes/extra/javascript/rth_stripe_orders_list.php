<?php

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

namespace RobinTheHood\Stripe;

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

// Only load on orders.php page
if (basename($_SERVER['PHP_SELF']) !== 'orders.php') {
    return;
}

// Include language file
$langCode = $_SESSION['language'] ?? 'german';
$langFile = DIR_FS_CATALOG . 'lang/' . $langCode . '/modules/payment/payment_rth_stripe.php';

if (file_exists($langFile)) {
    require_once($langFile);
}

$stripeOrderListName = defined('MODULE_PAYMENT_PAYMENT_RTH_STRIPE_TEXT_TITLE') ? constant('MODULE_PAYMENT_PAYMENT_RTH_STRIPE_TEXT_TITLE') : 'Stripe';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to mark temporary Stripe orders in the orders list
    function markTemporaryStripeOrders() {
        const orderRows = document.querySelectorAll('table.tableBoxCenter tbody tr:not(.dataTableHeadingRow)');
        
        orderRows.forEach(function(row) {
            const cells = row.querySelectorAll('td');
            if (cells.length < 6) return;
            
            // Column structure based on the HTML:
            // 0: Customer, 1: Order ID, 2: Country, 3: Total, 4: Date, 5: Payment Method, 6: Status, 7: Action
            const orderIdCell = cells[1]; // Order ID is in column 1
            const paymentMethodCell = cells[5]; // Payment Method is in column 5
            
            if (!orderIdCell || !paymentMethodCell) return;
            
            // Check if this row contains a Stripe payment and doesn't already have a badge
            if (paymentMethodCell.textContent.includes('<?= $stripeOrderListName ?>') && !paymentMethodCell.querySelector('.stripe-temp-order-indicator')) {
                // Get order ID from the order ID cell
                const orderIdMatch = orderIdCell.textContent.match(/\d+/);
                if (!orderIdMatch) return;
                
                const orderId = orderIdMatch[0];
                
                // Check if this is a temporary order by making an AJAX call
                fetch('rth_stripe.php?action=checkTemporaryOrder&order_id=' + orderId)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.isTemporary) {
                            // Add visual indicator to the row
                            row.classList.add('stripe-temp-order-row');
                            
                            // Add badge to payment method cell
                            const badge = document.createElement('span');
                            badge.className = 'stripe-temp-order-indicator';
                            badge.innerHTML = '<span class="stripe-temp-order-icon">⏳</span>Temp';
                            badge.title = 'Temporäre Stripe-Bestellung - Zahlung noch nicht abgeschlossen';
                            paymentMethodCell.appendChild(badge);
                        }
                    })
                    .catch(error => {
                        // Silent fail - don't spam console with errors
                    });
            }
        });
    }
    
    // Call the function when the page loads
    markTemporaryStripeOrders();
    
    // Also call it when the table content changes (pagination, sorting, etc.)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Check if new table rows were added
                const hasNewRows = Array.from(mutation.addedNodes).some(node => 
                    node.nodeType === 1 && (
                        node.tagName === 'TR' || 
                        node.querySelector('tr:not(.dataTableHeadingRow)')
                    )
                );
                if (hasNewRows) {
                    // Delay execution to ensure DOM is ready
                    setTimeout(markTemporaryStripeOrders, 200);
                }
            }
        });
    });
    
    // Observe changes to the table
    const mainTable = document.querySelector('table.tableBoxCenter');
    if (mainTable) {
        observer.observe(mainTable, {
            childList: true,
            subtree: true
        });
    }
});
</script>
