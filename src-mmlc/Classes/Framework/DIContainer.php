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

use RobinTheHood\Stripe\Classes\Config\StripeConfig;

class DIContainer extends Container
{
    public function __construct()
    {
        $this->registerDefinitions();
    }

    private function registerDefinitions(): void
    {
        // Special configuration for StripeConfig
        $this->set(
            StripeConfig::class,
            function () {
                return new StripeConfig('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');
            }
        );
    }
}
