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

// modified class order. We do this, because Order with a capital O looks nicer than order with small o
use order as ModifiedOrder;

/**
 * We wrap the modified Order in our own Order object so we can write Order with a capital O, it just looks nicer.
 * In addition, we can store additional information in our order object if required.
 * And we can access the required data OOP like.
 */
class Order
{
    /** @var int $modifiedOrderId */
    private $modifiedOrderId;

    /** @var ModifiedOrder $modifiedOrder */
    private $modifiedOrder;

    public function __construct(int $modifiedOrderId, ModifiedOrder $modifiedOrder)
    {
        $this->modifiedOrderId = $modifiedOrderId;

        if (!$modifiedOrder) {
            throw new OrderException('Can not create Order. No $modifiedOrder is empty');
        }

        if (!($modifiedOrder instanceof ModifiedOrder)) {
            throw new OrderException('Can not create Order. $modifiedOrder is not instance of ModifiedOrder');
        }

        $this->modifiedOrder = $modifiedOrder;
    }

    public function getId(): int
    {
        return $this->modifiedOrderId;
    }

    public function getTotal(): float
    {
        return $this->modifiedOrder->info['total'];
    }
}
