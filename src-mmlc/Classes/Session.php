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

/**
 * We need to save the current PHP session, as it may have already expired if the customer takes a long time
 * with the Stripe payment process. When the PHP session times out, the customer has paid, but no order is
 * placed in the shop.
 * 
 * The class should take over the task of saving and loading PHP session.
 */
class Session
{
    private const SESSION_PREFIX = 'rth_stripe';

    /**
     * @param Order $order Order is an object of our own Order class, not a modified order class
     */
    public function setOrder(Order $order)
    {
        $_SESSION[self::SESSION_PREFIX]['order'] = $order;
    }

    /**
     * The method should later save the current PHP session in a database table
     */
    public function save(string $sessionId = ''): string
    {
        if (!$sessionId) {
            $sessionId = $this->createSessionId();
        }

        $sessionData = serialize($_SESSION);
        // TODO ...

        return $sessionId;
    }

    /**
     * The method should later load a PHP session from a database table.
     */
    private function load(string $sessionId)
    {
        $sessionData = '';
        $_SESSION = unserialize($sessionData);
        // TODO ...
    }

    private function createSessionId(): string
    {
        return uniqid();
    }
}
