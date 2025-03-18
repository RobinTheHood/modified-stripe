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
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Framework;

use order as ModifiedOrder;
use RobinTheHood\ModifiedStdModule\Classes\StdModule;
use RuntimeException;

/**
 * In this class we have outsourced everything that is not for a specific modified PaymentModule. The methods in
 * this class can be useful for all kinds of PaymentModule classes.
 *
 * In addition, this class implements all methods that a payment module requires. This has the advantage that we
 * don't have to implement all the methods when we inherit from this PaymentModule class. We can only implement what
 * we need in a specific modified PaymentModule.
 *
 * The class also implements the PaymentModuleInterface interface. This isn't technically necessary, but is good OOP
 * style.
 */
class PaymentModule extends StdModule implements PaymentModuleInterface
{
    public function addKeys(array $keys): void
    {
        foreach ($keys as $key) {
            $this->addKey($key);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update_status(): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function pre_confirmation_check(): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function selection(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function confirmation(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function process_button(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function before_process(): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function payment_action(): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function before_send_order(): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function after_process(): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function success(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function get_error(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function iframeAction(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function javascript_validation(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function create_paypal_link()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function info()
    {
        return;
    }

    protected function getModifiedOrder(): ?ModifiedOrder
    {
        global $order;
        return $order;
    }

    /**
     * Gets a temprary order id
     *
     * @return int|false
     */
    protected function getTemporaryOrderId()
    {
        /** @var int|false */
        $tempOrderId = $_SESSION['tmp_oID'] ?? false;
        return $tempOrderId;
    }

    /**
     * Sets a temporary order id
     *
     * @param int|bool $tempOrderId
     */
    protected function setTemporaryOrderId($tempOrderId): void
    {
        $_SESSION['tmp_oID'] = $tempOrderId;
    }

    /**
     * Checks whether it is a valid order ID.
     *
     * @param mixed $orderId
     */
    protected function isValidOrderId($orderId): bool
    {
        return Order::isValidOrderId($orderId);
    }

    /**
     * Deletes an order based on the order id
     *
     * @param bool $restockOrder Add the inventory from the order back to the products
     * @param bool $reactiveProduct Activate the product if it has been deactivated
     */
    protected function removeOrder(
        int $orderId,
        bool $restockOrder = true,
        bool $reactiveProduct = true
    ): void {
        Order::removeOrder($orderId, $restockOrder, $reactiveProduct);
    }

    /**
     * Add a configuration field with a static field function
     */
    protected function addConfigurationStaticField(string $key, string $value, int $groupId, int $sortOrder, $setFunction): void
    {
        $setFunction = $setFunction . '(';
        $setFunction = str_replace('\\', '\\\\', $setFunction);
        $setFunction = xtc_db_input($setFunction);
        $this->addConfiguration($key, $value, $groupId, $sortOrder, $setFunction);
    }

    protected function updateConfigrationStaticFieldFunction(string $key, string $setFunction): void
    {
        $setFunction = $setFunction . '(';
        $setFunction = str_replace('\\', '\\\\', $setFunction);
        $setFunction = xtc_db_input($setFunction);
        $this->updateConfigurationSetFunction($key, $setFunction);
    }

    protected function updateConfigurationSetFunction(string $key, string $setFunction): void
    {
        $key = $this->getModulePrefix() . '_' . $key;

        $sql =
            "UPDATE `" . TABLE_CONFIGURATION . "`
            SET `set_function` = '$setFunction'
            WHERE `configuration_key` = '$key'";

        xtc_db_query($sql);
    }
}
