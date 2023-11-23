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

/**
 * This class is based on the request class from symfony so as not to reinvent the wheel.
 *
 * We pack all super global PHP request information in this class so that 1. we don't work with global variables in
 * further code and 2. so that we can inject our own request values into our controller for test purposes without
 * actually starting a request from a client . This is useful for unit testing.
 */
class Request
{
    /**
     * $_GET
     *
     * @var array
     */
    private $query;

    /**
     * $_POST
     *
     * @var array
     */
    private $request;

    /**
     * $_SERVER
     *
     * @var array
     */
    private $server;

    /**
     * @var string|resource|false|null
     */
    protected $content;

    /**
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->query   = $query;
        $this->request = $request;
        $this->server  = $server;
        $this->content = $content;
    }

    public function get($key)
    {
        return $this->query[$key] ?? '';
    }

    public function post($key)
    {
        return $this->request[$key] ?? '';
    }

    public function getServer($key)
    {
        return $this->server[$key] ?? '';
    }

    public function getContent()
    {
        return $this->content;
    }
}
