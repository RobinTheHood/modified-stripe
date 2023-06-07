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
 * A controller should not output its result directly itself. Instead, it wraps the result in a response object.
 * The framework code can then send the response object to the recipient (client) using the send() method.
 *
 * This is also useful for unit tests. We can examine the response object for tests and do not have to carry out
 * acceptance tests.
 */
class Response
{
    /** @var string */
    private $content;

    /** @var int */
    private $statusCode;

    public function __construct(string $content, int $statusCode = 200)
    {
        $this->content    = $content;
        $this->statusCode = $statusCode;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        echo $this->content;
        exit;
    }
}
