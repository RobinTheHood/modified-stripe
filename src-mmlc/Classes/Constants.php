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

class Constants
{
    public const MODULE_SYSTEM_NAME  = 'MODULE_SYSTEM_PAYMENT_RTH_STRIPE';
    public const MODULE_PAYMENT_NAME = 'MODULE_PAYMENT_PAYMENT_RTH_STRIPE';

    public const CONFIGURATION_CHECKOUT_TITLE = 'CHECKOUT_TITLE';
    public const CONFIGURATION_CHECKOUT_DESC  = 'CHECKOUT_DESC';
}
