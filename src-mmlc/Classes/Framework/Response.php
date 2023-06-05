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

class Response
{
    private $content;
    private $statusCode;

    public function __construct(string $content, int $statusCode = 200)
    {
        $this->content    = $content;
        $this->statusCode = $statusCode;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        echo $this->content;
        exit;
    }
}
