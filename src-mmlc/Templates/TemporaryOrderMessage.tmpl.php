<?php

/**
 * Temporary Order Information Template
 *
 * @var array $messages - Localized messages array
 */
?>
<div class="rth-stripe-temporary-order">
    <div class="info-icon">ℹ️</div>
    <div class="message-content">
        <h4><?php echo htmlspecialchars($messages['title']); ?></h4>
        <p><?php echo htmlspecialchars($messages['description']); ?></p>

        <div class="info-details">
            <p><strong><?php echo htmlspecialchars($messages['what_means']); ?></strong></p>
            <ul>
                <li><?php echo htmlspecialchars($messages['customer_checkout']); ?></li>
                <li><?php echo htmlspecialchars($messages['payment_not_completed']); ?></li>
                <li><?php echo htmlspecialchars($messages['auto_deleted']); ?>
                    <ul>
                        <li><?php echo htmlspecialchars($messages['customer_cancels']); ?></li>
                        <li><?php echo htmlspecialchars($messages['session_expires']); ?></li>
                        <li><?php echo htmlspecialchars($messages['payment_error']); ?></li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="action-info">
            <p><strong><?php echo htmlspecialchars($messages['what_to_do']); ?></strong></p>
            <p><?php echo htmlspecialchars($messages['no_action_needed']); ?></p>
        </div>
    </div>
</div>

<style>
    .rth-stripe-temporary-order {
        display: flex;
        background-color: #e1f5fe;
        border: 1px solid #0288d1;
        border-radius: 8px;
        padding: 20px;
        margin: 15px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    .rth-stripe-temporary-order .info-icon {
        font-size: 24px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .rth-stripe-temporary-order .message-content {
        flex: 1;
    }

    .rth-stripe-temporary-order h4 {
        color: #01579b;
        margin: 0 0 10px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .rth-stripe-temporary-order p {
        margin: 0 0 15px 0;
        color: #424242;
        line-height: 1.5;
    }

    .rth-stripe-temporary-order .info-details,
    .rth-stripe-temporary-order .action-info {
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 12px;
        margin: 10px 0;
    }

    .rth-stripe-temporary-order ul {
        margin: 8px 0;
        padding-left: 20px;
    }

    .rth-stripe-temporary-order li {
        margin: 4px 0;
        color: #424242;
    }

    .rth-stripe-temporary-order strong {
        color: #01579b;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .rth-stripe-temporary-order {
            flex-direction: column;
            text-align: center;
        }

        .rth-stripe-temporary-order .info-icon {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>
