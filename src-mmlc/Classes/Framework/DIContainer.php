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

use Exception;
use RobinTheHood\Stripe\Classes\Repository;
use RobinTheHood\Stripe\Classes\Repository\ConfigurationRepository;
use RobinTheHood\Stripe\Classes\Repository\OrderRepository;
use RobinTheHood\Stripe\Classes\Repository\OrderStatusHistoryRepository;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\Repository\SessionRepository;
use RobinTheHood\Stripe\Classes\Session;
use RobinTheHood\Stripe\Classes\Service\CheckoutService;
use RobinTheHood\Stripe\Classes\Service\PaymentCaptureService;
use RobinTheHood\Stripe\Classes\Service\SessionService;
use RobinTheHood\Stripe\Classes\Service\WebhookService;
use RobinTheHood\Stripe\Classes\StripeConfiguration;

class DIContainer
{
    private array $instances = [];

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function get(string $class)
    {
        // Return cached instance if available
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        // Create configuration for services
        if (StripeConfiguration::class === $class) {
            return $this->instances[$class] = new StripeConfiguration('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');
        }

        // Create database and repository related objects
        if (Database::class === $class) {
            return $this->instances[$class] = new Database();
        } elseif (Repository::class === $class) {
            return $this->instances[$class] = new Repository($this->get(Database::class));
        } elseif (ConfigurationRepository::class === $class) {
            return $this->instances[$class] = new ConfigurationRepository($this->get(Database::class));
        } elseif (OrderRepository::class === $class) {
            return $this->instances[$class] = new OrderRepository($this->get(Database::class));
        } elseif (OrderStatusHistoryRepository::class === $class) {
            return $this->instances[$class] = new OrderStatusHistoryRepository($this->get(Database::class));
        } elseif (PaymentRepository::class === $class) {
            return $this->instances[$class] = new PaymentRepository($this->get(Database::class));
        } elseif (SessionRepository::class === $class) {
            return $this->instances[$class] = new SessionRepository($this->get(Database::class));
        } elseif (Session::class === $class) {
            return $this->instances[$class] = new Session($this->get(Repository::class));
        }

        // Create service objects
        if (CheckoutService::class === $class) {
            return $this->instances[$class] = new CheckoutService($this, $this->get(StripeConfiguration::class));
        } elseif (SessionService::class === $class) {
            return $this->instances[$class] = new SessionService($this, $this->get(StripeConfiguration::class));
        } elseif (WebhookService::class === $class) {
            return $this->instances[$class] = new WebhookService($this, $this->get(StripeConfiguration::class));
        } elseif (PaymentCaptureService::class === $class) {
            return $this->instances[$class] = new PaymentCaptureService($this, $this->get(StripeConfiguration::class));
        }

        throw new Exception('Can not create object of type ' . $class);
    }
}
