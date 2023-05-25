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

use order as ModifiedOrder; // modified class order. We do this, because Order with a capital O looks nicer than order with small o

/**
 * We wrap the modified Order in our own Order object so we can write Order with a capital O, it just looks nicer.
 * In addition, we can store additional information in our order object if required.
 */
class Order
{
    /** @var ModifiedOrder $modifiedOrder */
    public $modifiedOrder = null;

    public static function createOrder(): Order
    {
        $order = new Order();
        $order->modifiedOrder = self::getModifiedOrder();
        return $order;
    }

    /**
     * This method returns a modified Order object only if the surrounding global code just created an $order. For
     * example in checkout_confirmation.php
     * 
     * It's not nice that we work with global code, maybe that can be improved. First of all, it cannot be avoided.
     */
    public static function getModifiedOrder(): ?ModifiedOrder
    {
        global $order;
        return $order;
    }
}
