<?php

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes;

use Exception;

/**
 * We need to save the current PHP session, as it may have already expired if the customer takes a long time
 * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
 * placed in the shop.
 *
 * The class should take over the task of saving and loading PHP session.
 */
class Session
{
    private const SESSION_PREFIX      = 'rth_stripe';
    private const SESSION_INDEX_ORDER = 'order';

    public function __construct()
    {
        // We need this, because modified classes are not loaded by the composer autoload
        // The classes that we want to unserialize must be loaded before we unserialize them
        require_once DIR_WS_CLASSES . 'order_total.php';
        require_once DIR_WS_CLASSES . 'order.php';
    }

    public function getOrder(): ?Order
    {
        $orderData = $_SESSION[self::SESSION_PREFIX][self::SESSION_INDEX_ORDER] ?? '';
        if (!$orderData) {
            return null;
        }

        return unserialize($orderData);
    }

    /**
     * @param Order $order - Order is an object of our own Order class, not a modified order class
     */
    public function setOrder(Order $order)
    {
        $_SESSION[self::SESSION_PREFIX][self::SESSION_INDEX_ORDER] = serialize($order);
    }

    /**
     * The method saves the current PHP session to the database
     */
    public function save(string $sessionId = ''): string
    {
        if (!$sessionId) {
            $sessionId = $this->createSessionId();
        }

        $sessionData = serialize($_SESSION);
        $sessionData = base64_encode($sessionData);

        $repo = new Repository();
        $repo->insertRthStripePhpSession($sessionId, $sessionData);

        return $sessionId;
    }

    /**
     * The method loads a PHP session from the database.
     */
    public function load(string $sessionId)
    {
        $repo = new Repository();

        $phpSession = $repo->getRthStripePhpSessionById($sessionId);
        if (!$phpSession) {
            throw new Exception("Can not find PhpSession with id: $sessionId");
        }

        if (!$phpSession['data']) {
            throw new Exception("PhpSession $sessionId is empty");
        }

        $sessionData = base64_decode($phpSession['data']);
        $session     = unserialize($sessionData);

        if (!$session) {
            throw new Exception("Can not unserialize PhpSession with id: $sessionId");
        }

        $_SESSION = $session;
    }

    private function createSessionId(): string
    {
        return 'sid_' . uniqid();
    }
}
