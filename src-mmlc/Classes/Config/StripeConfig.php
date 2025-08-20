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

namespace RobinTheHood\Stripe\Classes\Config;

use Exception;
use RobinTheHood\ModifiedStdModule\Classes\Configuration;
use RobinTheHood\Stripe\Classes\Repository\ConfigurationRepository;

/**
 * This class makes it easy to access the modified user configuration of the Stripe module. Unlike the pure
 * Configuration class, which creates many magic attributes, this class helps the IDE to show us autocompletion.
 *
 * @link https://github.com/RobinTheHood/modified-std-module#easy-access-with-class-configuration
 */
class StripeConfig extends Configuration
{
    private ConfigurationRepository $configurationRepo;

    public function __construct(ConfigurationRepository $configurationRepo)
    {
        parent::__construct('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');

        $this->configurationRepo = $configurationRepo;
    }

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

    /**
     * Get the correct secret key based on mode (sandbox or live)
     *
     * @return string
     */
    public function getActiveSecretKey(): string
    {
        return $this->getLiveMode()
            ? $this->getApiLiveSecret()
            : $this->getApiSandboxSecret();
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

    public function getOrderStatusAuthorized(int $default = 0): int
    {
        try {
            return (int) $this->orderStatusAuthorized;
        } catch (Exception $e) {
            return $default;
        }
    }

    public function getOrderStatusCaptured(int $default = 0): int
    {
        try {
            return (int) $this->orderStatusCaptured;
        } catch (Exception $e) {
            return $default;
        }
    }

    public function getOrderStatusCanceled(int $default = 0): int
    {
        try {
            return (int) $this->orderStatusCanceled;
        } catch (Exception $e) {
            return $default;
        }
    }

    public function getOrderStatusRefunded(int $default = 0): int
    {
        try {
            return (int) $this->orderStatusRefunded;
        } catch (Exception $e) {
            return $default;
        }
    }

    public function setWebhookSerect(string $secret): void
    {
        $this->configurationRepo->updateValue('MODULE_PAYMENT_PAYMENT_RTH_STRIPE_API_LIVE_ENDPOINT_SECRET', $secret);
    }

    public function getManualCapture(): bool
    {
        try {
            return 'true' === $this->manualCapture ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPaymentTitle(): string
    {
        try {
            return $this->paymentTitle;
        } catch (Exception $e) {
            return '';
        }
    }

    public function getPaymentDescription(): string
    {
        try {
            return $this->paymentDesc;
        } catch (Exception $e) {
            return '';
        }
    }

    public function getIconUrl(): string
    {
        try {
            return $this->iconUrl;
        } catch (Exception $e) {
            return '';
        }
    }

    public function getResetAutoIncrementAfterTempDelete(): bool
    {
        try {
            return 'true' === $this->resetAutoIncrementAfterTempDelete ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }
}
