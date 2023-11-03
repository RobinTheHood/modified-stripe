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
 * with the Stripe payment process. The class should help to simply restore the session
 *
 * The class should take over the task of saving and loading PHP session.
 *
 * The following files are required. See rth_stripe.php
 * DIR_WS_FUNCTIONS . 'sessions.php';
 * DIR_WS_MODULES . 'set_session_and_cookie_parameters.php';
 * DIR_WS_CLASSES . 'order_total.php';
 * DIR_WS_CLASSES . 'order.php';
 */
class Session
{
    private const SESSION_PREFIX      = 'rth_stripe';
    private const SESSION_INDEX_ORDER = 'order';

    private Repository $repo;

    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
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

        $this->repo->insertRthStripePhpSession($sessionId, $sessionData);

        return $sessionId;
    }

    /**
     * The method loads a PHP session from the database.
     */
    public function load(string $sessionId)
    {
        $phpSession = $this->repo->getRthStripePhpSessionById($sessionId);
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
