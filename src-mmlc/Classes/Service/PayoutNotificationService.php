<?php

/**
 * Stripe integration for modified
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Service;

use Exception;
use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Repository\ActionLogRepository;

class PayoutNotificationService
{
    private StripeConfig $stripeConfig;
    private StripePayoutService $stripePayoutService;
    private ActionLogRepository $actionLogRepository;

    public function __construct(
        StripeConfig $stripeConfig,
        StripePayoutService $stripePayoutService,
        ActionLogRepository $actionLogRepository
    ) {
        $this->stripeConfig = $stripeConfig;
        $this->stripePayoutService = $stripePayoutService;
        $this->actionLogRepository = $actionLogRepository;
    }

    /**
     * Executes the notification process.
     *
     * Return format:
     * [
     *   'sent' => [ payoutId, ... ],
     *   'skipped' => [ payoutId => reason, ... ],
     *   'errors' => [ payoutId => message, ... ]
     * ]
     *
     * @param int|null $sinceTimestamp Optional: only payouts newer than this timestamp
     * @return array<string,mixed>
     */
    public function process(?int $sinceTimestamp = null): array
    {
        $result = [
            'sent' => [],
            'skipped' => [],
            'errors' => [],
        ];

        // Feature enabled?
        if (!$this->stripeConfig->getPayoutNotifyEnable()) {
            $result['skipped']['_global'] = 'disabled';
            return $result;
        }

        // Parse recipients; if empty -> fallback to global shop address
        $recipients = $this->stripeConfig->parsePayoutNotifyRecipients();
        if (!$recipients) {
            $fallback = defined('STORE_OWNER_EMAIL_ADDRESS') ? (string) constant('STORE_OWNER_EMAIL_ADDRESS') : '';
            if ('' !== $fallback) {
                $recipients = [$fallback];
            }
        }

        if (!$recipients) {
            $result['skipped']['_global'] = 'no_recipients';
            return $result;
        }

        try {
            $payouts = $this->stripePayoutService->listNewPayouts($sinceTimestamp);
        } catch (Exception $e) {
            $result['errors']['_fetch'] = $e->getMessage();
            return $result;
        }

        foreach ($payouts as $payout) {
            $payoutId = $payout['id'];

            // Already sent?
            if ($this->actionLogRepository->exists('payout_email', $payoutId)) {
                $result['skipped'][$payoutId] = 'already_sent';
                continue;
            }

            $orders = $this->stripePayoutService->buildPayoutOrders($payoutId);

            // Generate email content
            $mailSubject = 'Stripe Auszahlung ' . $payoutId;
            $mailBody = $this->buildEmailBody($payout, $orders);

            $allSent = true;
            foreach ($recipients as $recipient) {
                $sent = $this->sendMail($recipient, $mailSubject, $mailBody);
                if (!$sent) {
                    $allSent = false;
                    $result['errors'][$payoutId] = 'mail_failed:' . $recipient;
                }
            }

            if ($allSent) {
                $this->actionLogRepository->add('payout_email', $payoutId, ['status' => 'sent']);
                $result['sent'][] = $payoutId;
            }
        }

        return $result;
    }

    /**
     * Builds a simple HTML mail body.
     *
     * @param array<string,mixed> $payout
     * @param array<int,array<string,mixed>> $orders
     */
    private function buildEmailBody(array $payout, array $orders): string
    {
        $amountFormatted = number_format($payout['amount'] / 100, 2, ',', '.');
        $html = [];
        $html[] = '<h1>Stripe Auszahlung</h1>';
        $html[] = '<p><strong>ID:</strong> ' . htmlspecialchars($payout['id']) . '<br>';
        $html[] = '<strong>Betrag:</strong> ' . $amountFormatted . ' ' . strtoupper($payout['currency']) . '<br>';
        $html[] = '<strong>Status:</strong> ' . htmlspecialchars($payout['status']) . '<br>';
        $html[] = '<strong>Created:</strong> ' . date('Y-m-d H:i:s', (int) $payout['created']) . '<br>';
        $html[] = '<strong>Arrival:</strong> ' . date('Y-m-d H:i:s', (int) $payout['arrivalDate']) . '</p>';

        if (!$orders) {
            $html[] = '<p>Keine zugeordneten Bestellungen ermittelt.</p>';
        } else {
            $html[] = '<h2>Bestellungen</h2>';
            $html[] = '<table border="1" cellpadding="4" cellspacing="0">';
            $html[] = '<tr><th>Order ID</th><th>Kunde</th><th>Datum</th><th>Betrag</th><th>Währung</th></tr>';
            foreach ($orders as $order) {
                $html[] = '<tr>'
                    . '<td>' . (int) $order['orderId'] . '</td>'
                    . '<td>' . htmlspecialchars((string) $order['customerName']) . '</td>'
                    . '<td>' . htmlspecialchars((string) $order['orderDate']) . '</td>'
                    . '<td style="text-align:right">' . number_format(((int) $order['amount']) / 100, 2, ',', '.') . '</td>'
                    . '<td>' . htmlspecialchars(strtoupper((string) $order['currency'])) . '</td>'
                    . '</tr>';
            }
            $html[] = '</table>';
        }

        return implode("\n", $html);
    }

    /**
     * Sends an email. Encapsulation for future extension.
     */
    private function sendMail(string $to, string $subject, string $htmlBody): bool
    {
        if (!function_exists('xtc_php_mail')) {
            return false;
        }

        $fromEmail = defined('STORE_OWNER_EMAIL_ADDRESS') ? (string) constant('STORE_OWNER_EMAIL_ADDRESS') : 'no-reply@example.com';
        $fromName = defined('STORE_OWNER') ? (string) constant('STORE_OWNER') : 'Shop';
        $plainBody = strip_tags($htmlBody);

        $ok = xtc_php_mail(
            $fromEmail,
            $fromName,
            $to,
            '',
            '',
            $fromEmail,
            $fromName,
            '',
            '',
            $subject,
            $htmlBody,
            $plainBody
        );

        return (bool) $ok;
    }
}
