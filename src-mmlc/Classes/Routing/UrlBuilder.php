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

namespace RobinTheHood\Stripe\Classes\Routing;

use RobinTheHood\Stripe\Classes\Framework\Constant;

/**
 * With this class we can globally adjust the required URLs throughout the module if necessary.
 */
class UrlBuilder
{
    public function getShopBase(): string
    {
        return Constant::getHttpsServer() . Constant::getDirWsCatalog();
    }

    public function getFormActionUrl(): string
    {
        return $this->getShopBase() . 'rth_stripe.php?action=checkout';
    }

    public function getStripeSuccess(): string
    {
        return $this->getShopBase() . 'rth_stripe.php?action=success&session_id={CHECKOUT_SESSION_ID}';
    }

    public function getStripeCancel(): string
    {
        return $this->getShopBase() . 'rth_stripe.php?action=cancel';
    }

    public function getStripeWebhook(): string
    {
        return $this->getShopBase() . 'rth_stripe.php?action=receiveHook';
    }

    public function getShoppingCart(): string
    {
        return $this->getShopBase() . 'shopping_cart.php';
    }

    public function getCheckoutProcess(): string
    {
        return $this->getShopBase() . 'checkout_process.php';
    }

    public function getCheckoutConfirmation(): string
    {
        return $this->getShopBase() . 'checkout_confirmation.php';
    }

    public function getAdminOrders(): string
    {
        return $this->getShopBase() . Constant::getDirAdmin() . 'orders.php';
    }
}
