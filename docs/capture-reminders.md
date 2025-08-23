# Capture Reminder Email Feature

This feature adds automated reminder emails for Stripe payments that are approaching their capture deadline.

## Overview

When payments are authorized but not yet captured, merchants have a limited time window (typically 7 days) to capture the funds before they expire. This feature automatically sends reminder emails 24 hours before the capture deadline to help merchants avoid losing access to authorized funds.

## Setup

### For New Installations

The feature is automatically enabled for new installations. The database table will include the necessary `reminder_sent_at` column.

### For Existing Installations  

If upgrading from a previous version, you need to update the database schema:

1. Call the schema update endpoint once:
   ```
   GET https://yourstore.com/rth_stripe.php?action=updateSchema
   ```

This will add the `reminder_sent_at` column to the `rth_stripe_payment` table.

## Automated Checking

Set up a cron job to regularly check for payments nearing their capture deadline:

```bash
# Check every hour for payments requiring reminders
0 * * * * curl -s "https://yourstore.com/rth_stripe.php?action=checkCaptureReminders"
```

### Cron Job Response

The endpoint returns JSON with the results:

```json
{
  "success": true,
  "reminders_sent": 2,
  "message": "Checked capture reminders. Sent 2 reminder(s)."
}
```

## How It Works

1. **Payment Scanning**: The system checks all payments that haven't had reminder emails sent
2. **Deadline Calculation**: Uses the same logic as the admin interface to calculate capture deadlines
3. **Timing Check**: Sends reminders when between 1-24 hours remain until deadline
4. **Email Delivery**: Sends HTML email to the merchant with order and deadline details
5. **Tracking**: Marks payments as having received reminders to prevent duplicates

## Email Content

Reminder emails include:
- Order ID and customer email
- Payment amount and currency
- Stripe Payment Intent ID
- Capture deadline date/time
- Remaining time until deadline
- Action instructions

## Email Configuration

The system attempts to use the store's configured email address in this order:
1. `STORE_OWNER_EMAIL_ADDRESS` from store configuration
2. Fallback to `admin@yourdomain.com`

## Technical Details

### Database Changes

Adds `reminder_sent_at` column to `rth_stripe_payment` table:
```sql
ALTER TABLE `rth_stripe_payment` 
ADD COLUMN `reminder_sent_at` datetime DEFAULT NULL
```

### New Classes

- `CaptureReminderService` - Core reminder logic
- Extended `PaymentRepository` - Database operations for reminders
- New controller actions in `Controller.php`

### Requirements

- Payments must have `capture_method` set to `manual`
- Payments must be in `requires_capture` status
- No previous reminder must have been sent for the payment

## Troubleshooting

### No Emails Received

1. Check cron job is running: `curl "https://yourstore.com/rth_stripe.php?action=checkCaptureReminders"`
2. Verify email configuration in store settings
3. Check server mail logs for delivery issues
4. Ensure payments are in `requires_capture` status

### Schema Update Issues

If the schema update fails:
1. Manually add the column:
   ```sql
   ALTER TABLE `rth_stripe_payment` ADD COLUMN `reminder_sent_at` datetime DEFAULT NULL;
   ```
2. Verify the column was added:
   ```sql
   SHOW COLUMNS FROM `rth_stripe_payment` LIKE 'reminder_sent_at';
   ```