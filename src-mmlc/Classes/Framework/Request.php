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

class Request
{
    private $query;
    private $request;

    public function __construct(array $query, array $request)
    {
        $this->query   = $query;
        $this->request = $request;
    }

    public function get($key)
    {
        return $this->query[$key];
    }

    public function post($key)
    {
        return $this->request[$key];
    }
}
