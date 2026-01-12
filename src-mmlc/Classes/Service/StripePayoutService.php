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
use Stripe\BalanceTransaction;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePayoutService
{
    private const DEFAULT_LIMIT = 100; // Safety limit

    private StripeClient $stripeClient;
    private ?OrderRepository $orderRepository = null; // Optional: injected later

    public function __construct(StripeConfig $stripeConfig, ?OrderRepository $orderRepository = null)
    {
        $secretKey = $stripeConfig->getActiveSecretKey();
        if ('' === $secretKey) {
            throw new Exception('Stripe secret key not configured');
        }

        $this->stripeClient = new StripeClient(['api_key' => $secretKey]);
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
        $max = $limit ?? self::DEFAULT_LIMIT;
        if ($max <= 0) {
            $max = self::DEFAULT_LIMIT;
        }

        // Basic fetch: simple pagination strategy (single fetch up to limit)
        $params = [
            'limit' => $max,
        ];

        $payoutCollection = $this->stripeClient->payouts->all($params);
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

        // Retrieve balance transactions for the payout
        try {
            $balanceTxCollection = $this->stripeClient->balanceTransactions->all([
                'payout' => $payoutId,
                'limit'  => 100,
                'expand' => [
                    'data.source', // liefert z.B. Charge-Objekt statt nur ID
                    'data.source.payment_intent', // optional: direkt PI mitladen
                ],
            ]);
        } catch (\Exception $e) {
            return [];
        }

        $orders = [];
        $seenOrderIds = [];

        foreach ($balanceTxCollection->data as $balanceTx) {
            $source = $balanceTx->source ?? null;

            // Nur "charge" ist für Bestellungen relevant (Refunds etc. ggf. separat behandeln)
            if (!$source || !is_object($source) || ($source->object ?? null) !== 'charge') {
                continue;
            }

            $paymentIntent = $source->payment_intent ?? null;

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

    private function getPaymentIntentFromBalanceTransaction(BalanceTransaction $balanceTransaction): ?PaymentIntent
    {
        $sourceId = $balanceTransaction->source;
        if (!$sourceId) {
            return null;
        }

        // Wir können nur Charge-ähnliche Quellen behandeln (ch_... oder py_...)
        if (!preg_match('/^(ch_|py_)/', $sourceId)) {
            return null;
        }

        // PaymentIntent direkt mit expand holen, so sparen wir einen API-Call
        try {
            $charge = $this->stripeClient->charges->retrieve($sourceId, [
                'expand' => ['payment_intent'],
            ]);
        } catch (\Exception $e) {
            return null;
        }

        $paymentIntent = $charge->payment_intent ?? null;

        if ($paymentIntent instanceof PaymentIntent) {
            return $paymentIntent;
        }

        return null;
    }
}
