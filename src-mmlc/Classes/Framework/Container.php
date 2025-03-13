<?php

/**
 * Generic DI Container with autowiring support
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Framework;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionParameter;

class Container
{
    protected array $instances = [];
    protected array $definitions = [];

    /**
     * Register a factory function for creating a service
     *
     * @param string $name The service identifier
     * @param Closure $factory The factory function
     */
    public function set(string $name, Closure $factory): void
    {
        $this->definitions[$name] = $factory;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     * @throws Exception
     */
    public function get(string $class)
    {
        // Return cached instance if available
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        // Use definition if available
        if (isset($this->definitions[$class])) {
            return $this->instances[$class] = $this->definitions[$class]();
        }

        // Use autowiring
        return $this->instances[$class] = $this->autowire($class);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     * @throws Exception
     */
    protected function autowire(string $class)
    {
        try {
            $reflectionClass = new ReflectionClass($class);

            if (!$reflectionClass->isInstantiable()) {
                throw new Exception("Class $class is not instantiable");
            }

            $constructor = $reflectionClass->getConstructor();

            if (null === $constructor) {
                return new $class();
            }

            $parameters = $constructor->getParameters();
            $dependencies = $this->resolveDependencies($parameters);

            return $reflectionClass->newInstanceArgs($dependencies);
        } catch (Exception $e) {
            throw new Exception("Cannot autowire class $class: " . $e->getMessage());
        }
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return array
     * @throws Exception
     */
    protected function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (null === $type || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve parameter {$parameter->getName()}");
                }
            } else {
                $className = $type->getName();
                $dependencies[] = $this->get($className);
            }
        }

        return $dependencies;
    }
}
