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
use RobinTheHood\Stripe\Classes\Repository\OrderRepository;

class StripePayoutService
{
    private const DEFAULT_LIMIT = 100; // Safety limit

    private StripeConfig $stripeConfig;
    private ?OrderRepository $orderRepository = null; // Optional: injected later

    public function __construct(StripeConfig $stripeConfig, ?OrderRepository $orderRepository = null)
    {
        $this->stripeConfig = $stripeConfig;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Returns new payouts. Optionally filters out payouts whose created/arrival_date
     * are less than or equal to the given timestamp.
     *
     * @param int|null $sinceTimestamp Unix timestamp (UTC) – if set, only payouts with reference time > since are returned
     * @param int|null $limit Max number of payouts (fallback DEFAULT_LIMIT)
     *
     * @return array<int, array{id:string,amount:int,currency:string,created:int,arrivalDate:int,status:string}>
     * @throws Exception If the API secret key is not configured
     */
    public function listNewPayouts(?int $sinceTimestamp = null, ?int $limit = null): array
    {
        $secretKey = $this->stripeConfig->getActiveSecretKey();
        if ('' === $secretKey) {
            throw new Exception('Stripe secret key not configured');
        }

        \Stripe\Stripe::setApiKey($secretKey);

        $max = $limit ?? self::DEFAULT_LIMIT;
        if ($max <= 0) {
            $max = self::DEFAULT_LIMIT;
        }

        // Basic fetch: simple pagination strategy (single fetch up to limit)
        $params = [
            'limit' => $max,
        ];

        $payoutCollection = \Stripe\Payout::all($params);
        $result = [];

        foreach ($payoutCollection->data as $payout) {
            // $payout is a \Stripe\Payout instance
            // Client-side sinceTimestamp filter – use the larger of created and arrival_date as reference.
            $referenceTime = max((int) $payout->created, (int) $payout->arrival_date);
            if (null !== $sinceTimestamp && $referenceTime <= $sinceTimestamp) {
                continue; // Already known / too old
            }

            $result[] = [
                'id' => (string) $payout->id,
                'amount' => (int) $payout->amount, // kleinste Einheit (cent)
                'currency' => (string) $payout->currency,
                'created' => (int) $payout->created,
                'arrivalDate' => (int) $payout->arrival_date,
                'status' => (string) $payout->status,
            ];
        }

        // Sort ascending by arrival date (fallback created) for deterministic processing
        usort($result, function (array $a, array $b): int {
            $aRef = $a['arrivalDate'] ?: $a['created'];
            $bRef = $b['arrivalDate'] ?: $b['created'];
            if ($aRef === $bRef) {
                return 0;
            }
            return ($aRef < $bRef) ? -1 : 1;
        });

        return $result;
    }

    /**
     * Returns a list of related shop orders (order summaries) for a given payout.
     * Return format:
     * [
     *   ['orderId'=>int, 'customerName'=>string, 'orderDate'=>string (Y-m-d H:i:s), 'amount'=>int, 'currency'=>string]
     * ]
     *
     * Note: If no OrderRepository was injected, an empty array is returned.
     *
     * @param string $payoutId Stripe Payout ID (e.g. po_...)
     * @return array<int,array<string,mixed>>
     */
    public function buildPayoutOrders(string $payoutId): array
    {
        if (null === $this->orderRepository) {
            return [];
        }

        $secretKey = $this->stripeConfig->getActiveSecretKey();
        if ('' === $secretKey) {
            return [];
        }
        \Stripe\Stripe::setApiKey($secretKey);

        // Retrieve balance transactions for the payout
        try {
            $balanceTxCollection = \Stripe\BalanceTransaction::all([
                'payout' => $payoutId,
                'limit' => 100,
            ]);
        } catch (\Exception $e) {
            return [];
        }

        $orders = [];
        $seenOrderIds = [];

        foreach ($balanceTxCollection->data as $balanceTx) {
            // We only care about charge transactions (typical payment flow)
            if ('charge' !== $balanceTx->type) {
                continue;
            }

            $source = $balanceTx->source; // kann Charge-ID sein
            if (!$source) {
                continue;
            }

            try {
                $charge = \Stripe\Charge::retrieve([
                    'id' => $source,
                    'expand' => ['payment_intent'],
                ]);
            } catch (\Exception $e) {
                continue; // Charge could not be retrieved → skip
            }

            if (!isset($charge->payment_intent) || !$charge->payment_intent) {
                continue;
            }

            $paymentIntent = $charge->payment_intent; // Expanded object or ID
            // If only ID (string), expand failed → skip
            if (is_string($paymentIntent)) {
                continue;
            }

            $metadata = $paymentIntent->metadata ?? null;
            if (!$metadata || !isset($metadata->order_id)) {
                continue; // No order mapping
            }

            $orderId = (int) $metadata->order_id;
            if ($orderId <= 0 || isset($seenOrderIds[$orderId])) {
                continue;
            }

            $orderRow = $this->orderRepository->findById($orderId);
            if (!$orderRow) {
                continue; // Order does not exist (possibly deleted)
            }

            $customerName = trim(($orderRow['customers_firstname'] ?? '') . ' ' . ($orderRow['customers_lastname'] ?? ''));
            $orderDate = $orderRow['date_purchased'] ?? '';

            $orders[] = [
                'orderId' => $orderId,
                'customerName' => $customerName,
                'orderDate' => $orderDate,
                'amount' => (int) $balanceTx->amount, // In smallest unit (cents)
                'currency' => (string) $balanceTx->currency,
            ];
            $seenOrderIds[$orderId] = true;
        }

        // Sort orders by orderId (or orderDate if desired)
        usort($orders, function (array $a, array $b): int {
            if ($a['orderId'] === $b['orderId']) {
                return 0;
            }
            return ($a['orderId'] < $b['orderId']) ? -1 : 1;
        });

        return $orders;
    }
}
