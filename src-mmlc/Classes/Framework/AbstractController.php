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
 * The AbstractController can automatically forward requests to methods beginning with the invoke prefix via the ?action=
 * query parameter in the URL. If action is empty or not set, invokeIndex() is called by default.
 * The entry point of this class is in file shop-root/rth_stripe.php
 */
abstract class AbstractController
{
    /**
     * @var AbstractController[]
     */
    private array $controllers = [];

    public function __construct()
    {
        $this->addController($this);
    }

    public function invoke(Request $request): Response
    {
        $action = $this->getAction($request);
        if (!$action) {
            $action = 'Index';
        }

        $invokeMethod = 'invoke' . ucfirst($action);

        foreach ($this->controllers as $controller) {
            $controller->preInvoke($request);
            if (method_exists($controller, $invokeMethod)) {
                $response = $controller->$invokeMethod($request);
            }
            $controller->postInvoke($request);
        }

        return $response;
    }

    /**
     * @return string name of action
     */
    public function getAction(Request $request): string
    {
        $action = $request->get('action') ?? '';
        if (!$action) {
            $action = $request->post('action') ?? '';
        }
        return $action;
    }

    public function addController(AbstractController $contoller): void
    {
        $this->controllers[] = $contoller;
    }

    public function preInvoke(Request $request): void
    {
    }

    public function postInvoke(Request $request): void
    {
    }
}
