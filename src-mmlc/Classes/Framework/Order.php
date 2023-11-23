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

namespace RobinTheHood\Stripe\Classes\Framework;

// modified class order. We do this, because Order with a capital O looks nicer than order with small o
use order as ModifiedOrder;
use RuntimeException;

require_once DIR_FS_INC . 'xtc_remove_order.inc.php';
require_once DIR_WS_CLASSES . 'order.php';
require_once DIR_WS_CLASSES . 'order_total.php';

/**
 * We wrap the modified Order in our own Order object so we can write Order with a capital O, it just looks nicer.
 * In addition, we can store additional information in our order object if required.
 * And we can access the required data OOP like.
 *
 * Dependencies:
 *      DIR_WS_CLASSES . 'order_total.php';
 *      DIR_WS_CLASSES . 'order.php';
 *      DIR_FS_INC . 'xtc_remove_order.inc.php';
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

        // if (!$modifiedOrder) {
        //     throw new OrderException('Can not create Order. No $modifiedOrder is empty');
        // }

        if (!($modifiedOrder instanceof ModifiedOrder)) {
            throw new OrderException('Can not create Order. $modifiedOrder is not instance of ModifiedOrder');
        }

        $this->modifiedOrder = $modifiedOrder;
    }

    /**
     * Deletes an order based on the order id
     *
     * @param bool $restockOrder Add the inventory from the order back to the products
     * @param bool $reactiveProduct Activate the product if it has been deactivated
     */
    public static function removeOrder(
        int $orderId,
        bool $restockOrder = true,
        bool $reactiveProduct = true
    ): void {
        if (!self::isValidOrderId($orderId)) {
            throw new RuntimeException("Can not remove order. $orderId is not a valid order id");
        }

        $restockOrderParamValue = $restockOrder ? 'on' : false;
        xtc_remove_order($orderId, $restockOrderParamValue, $reactiveProduct);
    }


    /**
     * Checks whether it is a valid order ID.
     *
     * @param mixed $orderId
     */
    public static function isValidOrderId($orderId): bool
    {
        if (!$orderId) {
            return false;
        }

        if (!is_numeric($orderId)) {
            return false;
        }

        return true;
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
