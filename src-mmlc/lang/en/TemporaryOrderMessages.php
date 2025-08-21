<?php

/**
 * English language file for Stripe temporary order messages
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

return [
    'title' => 'Temporary Order',
    'description' => 'This order is not yet linked to a Stripe payment. This is a normal state for orders that have just been created.',
    'what_means' => 'What does this mean?',
    'customer_checkout' => 'The customer is still in the Stripe checkout process',
    'payment_not_completed' => 'Payment has not been completed or confirmed yet',
    'auto_deleted' => 'This order will be automatically deleted if:',
    'customer_cancels' => 'The customer cancels the payment',
    'session_expires' => 'The Stripe checkout session expires (usually after 24 hours)',
    'payment_error' => 'An error occurs in the payment process',
    'what_to_do' => 'What do you need to do?',
    'no_action_needed' => 'Usually no action is required. If the customer successfully completes the payment, this order will automatically be linked to the Stripe payment details and the status will be updated accordingly. If this is not the case, please check the order and cancel it if necessary.',
];
