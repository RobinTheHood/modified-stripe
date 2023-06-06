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
}
