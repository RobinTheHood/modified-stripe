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
use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Controller\AdminController;
use RobinTheHood\Stripe\Classes\Controller\Controller;
use RobinTheHood\Stripe\Classes\Repository\ConfigurationRepository;
use RobinTheHood\Stripe\Classes\Repository\OrderRepository;
use RobinTheHood\Stripe\Classes\Repository\OrderStatusHistoryRepository;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\Repository\PhpSessionRepository;
use RobinTheHood\Stripe\Classes\Routing\UrlBuilder;
use RobinTheHood\Stripe\Classes\Service\CheckoutService;
use RobinTheHood\Stripe\Classes\Service\PaymentCaptureService;
use RobinTheHood\Stripe\Classes\Service\SessionService;
use RobinTheHood\Stripe\Classes\Service\WebhookService;
use RobinTheHood\Stripe\Classes\Storage\PhpSession;
use RobinTheHood\Stripe\Classes\StripeEventHandler;

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

        // Create controller objects
        if (AdminController::class === $class) {
            return $this->instances[$class] = new AdminController(
                $this->get(StripeConfig::class),
                $this->get(PaymentRepository::class)
            );
        } elseif (Controller::class === $class) {
            return $this->instances[$class] = new Controller(
                $this->get(CheckoutService::class),
                $this->get(SessionService::class),
                $this->get(WebhookService::class),
                $this->get(PaymentCaptureService::class),
                $this->get(UrlBuilder::class),
            );
        }

        // Create configuration for services
        if (StripeConfig::class === $class) {
            return $this->instances[$class] = new StripeConfig('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');
        }

        // Create database and repository related objects
        if (Database::class === $class) {
            return $this->instances[$class] = new Database();
        } elseif (ConfigurationRepository::class === $class) {
            return $this->instances[$class] = new ConfigurationRepository($this->get(Database::class));
        } elseif (OrderRepository::class === $class) {
            return $this->instances[$class] = new OrderRepository($this->get(Database::class));
        } elseif (OrderStatusHistoryRepository::class === $class) {
            return $this->instances[$class] = new OrderStatusHistoryRepository($this->get(Database::class));
        } elseif (PaymentRepository::class === $class) {
            return $this->instances[$class] = new PaymentRepository($this->get(Database::class));
        } elseif (PhpSessionRepository::class === $class) {
            return $this->instances[$class] = new PhpSessionRepository($this->get(Database::class));
        } elseif (PhpSession::class === $class) {
            return $this->instances[$class] = new PhpSession(
                $this->get(PhpSessionRepository::class)
            );
        }

        // Create service objects
        if (CheckoutService::class === $class) {
            return $this->instances[$class] = new CheckoutService(
                $this->get(PhpSession::class),
                $this->get(StripeConfig::class),
                $this->get(UrlBuilder::class),
            );
        } elseif (SessionService::class === $class) {
            return $this->instances[$class] = new SessionService(
                $this->get(PhpSession::class),
                $this->get(StripeConfig::class)
            );
        } elseif (WebhookService::class === $class) {
            return $this->instances[$class] = new WebhookService(
                $this->get(StripeEventHandler::class),
                $this->get(StripeConfig::class)
            );
        } elseif (PaymentCaptureService::class === $class) {
            return $this->instances[$class] = new PaymentCaptureService(
                $this->get(PaymentRepository::class),
                $this->get(StripeConfig::class)
            );
        }

        if (StripeEventHandler::class === $class) {
            return $this->instances[$class] = new StripeEventHandler(
                $this->get(OrderRepository::class),
                $this->get(OrderStatusHistoryRepository::class),
                $this->get(PaymentRepository::class),
                $this->get(PhpSession::class),
                $this->get(StripeConfig::class)
            );
        }

        if (UrlBuilder::class === $class) {
            return $this->instances[$class] = new UrlBuilder();
        }

        throw new Exception('Can not create object of type ' . $class);
    }
}
