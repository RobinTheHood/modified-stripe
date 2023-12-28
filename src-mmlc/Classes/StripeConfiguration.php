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

use Exception;
use RobinTheHood\ModifiedStdModule\Classes\Configuration;

/**
 * This class makes it easy to access the modified user configuration of the Stripe module. Unlike the pure
 * Configuration class, which creates many magic attributes, this class helps the IDE to show us autocompletion.
 *
 * @link https://github.com/RobinTheHood/modified-std-module#easy-access-with-class-configuration
 */
class StripeConfiguration extends Configuration
{
    public function getLiveMode(): bool
    {
        try {
            return 'true' === $this->liveMode ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getApiSandboxSecret(): string
    {
        try {
            return $this->apiSandboxSecret;
        } catch (Exception $e) {
            return '';
        }
    }

    public function getApiLiveSecret(): string
    {
        try {
            return $this->apiLiveSecret;
        } catch (Exception $e) {
            return '';
        }
    }

    public function getApiLiveEndpointSecret(): string
    {
        try {
            return $this->apiLiveEndpointSecret;
        } catch (Exception $e) {
            return '';
        }
    }

    public function getOrderStatusPending(int $default = 0): int
    {
        try {
            return (int) $this->orderStatusPending;
        } catch (Exception $e) {
            return $default;
        }
    }

    public function getOrderStatusPaid(int $default = 0): int
    {
        try {
            return (int) $this->orderStatusPaid;
        } catch (Exception $e) {
            return $default;
        }
    }
}
