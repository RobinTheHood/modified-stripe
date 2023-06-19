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

/**
 * We created this interface so that we and the software can see at first glance which methods a modified Payment
 * Module can implement.
 *
 * You can find more information about modified payment modules in our documentation at:
 * @link https://docs.module-loader.de/references/module-classes/concrete/payment/
 */
interface PaymentModuleInterface
{
    public function update_status(): void;

    public function pre_confirmation_check(): void;

    /**
     * @return array SelectionArray
     */
    public function selection(): array;

    /**
     * @return array ConfirmationArray
     */
    public function confirmation(): array;

    public function process_button(): string;

    public function before_process(): void;

    public function payment_action(): void;

    public function before_send_order(): void;

    public function after_process(): void;

    /**
     * @return array SuccsessArray
     */
    public function success(): array;

    /**
     * @return array ErrorArray
     */
    public function get_error(): array;

    public function iframeAction(): string;

    public function javascript_validation(): string;

    public function create_paypal_link();

    /**
     * @return mixed
     */
    public function info();
}
