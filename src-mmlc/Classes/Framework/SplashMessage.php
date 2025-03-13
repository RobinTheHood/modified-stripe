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

use messageStack;

/**
 * Wrapper for modified class messageStack
 *
 * Dependencies:
 *      DIR_WS_CLASSES . 'message_stack.php';
 */
class SplashMessage
{
    /**
     * @var string TYPE_ERROR
     */
    public const TYPE_ERROR = 'error';

    /**
     * @var string TYPE_WARNING
     */
    public const TYPE_WARNING = 'warning';

    /**
     * @var string TYPE_SUCCESS
     */
    public const TYPE_SUCCESS = 'success';

    /**
     * @var SplashMessage
     */
    private static $splashMessage;

    private function __construct()
    {
    }

    public static function getInstance(): SplashMessage
    {
        if (self::$splashMessage) {
            return self::$splashMessage;
        }
        self::$splashMessage = new SplashMessage();
        return self::$splashMessage;
    }

    public function error(string $class, string $message): void
    {
        $this->addMessage($class, $message, self::TYPE_ERROR);
    }

    public function success(string $class, string $message): void
    {
        $this->addMessage($class, $message, self::TYPE_SUCCESS);
    }

    private function addMessage(string $class, string $message, string $type): void
    {
        $messageStack = $this->getMessageStack();
        $messageStack->add_session($class, $message, $type);
    }

    private function getMessageStack(): messageStack
    {
        global $messageStack;
        if ($messageStack instanceof messageStack) {
            return $messageStack;
        }
        return new messageStack();
    }

    public function addAdminMessage($message, $type = self::TYPE_ERROR): void
    {
        if (!isset($_SESSION['messageToAdminStack'])) {
            $_SESSION['messageToAdminStack'] = [];
        }

        if (!isset($_SESSION['messageToAdminStack'][$type])) {
            $_SESSION['messageToAdminStack'][$type] = [];
        }

        $_SESSION['messageToAdminStack'][$type][md5($message)] = $message;
    }
}
