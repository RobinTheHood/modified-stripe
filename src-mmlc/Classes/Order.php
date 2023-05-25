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
 * And we can access the required data OOP like.
 */
class Order
{
    /** @var ModifiedOrder $modifiedOrder */
    private $modifiedOrder;

    public function __construct()
    {
        $modifiedOrder = $this->loadModifiedOrder();
        if (!$modifiedOrder) {
            throw new OrderException('Can not create Order. No modifed order object found.');
        }
    
        $this->modifiedOrder = $modifiedOrder;
    }

    public function getTotal(): float
    {
        return $this->modifiedOrder->info['total'];
    }

    /**
     * This method returns a modified Order object only if the surrounding global code just created an $order. For
     * example in checkout_confirmation.php
     * 
     * It's not nice that we work with global code, maybe that can be improved. First of all, it cannot be avoided.
     */
    public function loadModifiedOrder(): ?ModifiedOrder
    {
        global $order;

        if (!($order instanceof ModifiedOrder)) {
            return null;
        }

        return $order;
    }
}
