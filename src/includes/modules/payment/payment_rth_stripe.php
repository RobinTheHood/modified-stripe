<?php

use RobinTheHood\ModifiedStdModule\Classes\StdModule;

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */
class payment_rth_stripe extends StdModule
{
    public const VERSION = '0.1.0';
    public const NAME    = 'MODULE_PAYMENT_RTH_STRIPE';

    /**
     * Configuration keys which are automatically added/removed on
     * install/remove
     *
     * $keys is already used by the StdModule, so we need to use a different
     * variable.
     *
     * @var array
     */
    public static array $configurationKeys = [
        'API_SANDBOX_KEY',
        'API_SANDBOX_SECRET',
        'API_LIVE_KEY',
        'API_LIVE_SECRET',
    ];

    public static function setFunction($function, $value, $option): string
    {
        return call_user_func(base64_decode($function), $value, $option);
    }

    public function __construct()
    {
        parent::__construct(self::NAME);

        $this->checkForUpdate(true);

        foreach (self::$configurationKeys as $key) {
            $this->addKey($key);
        }
    }

    public function install(): void
    {
        parent::install();

        /**
         * Namespaces are encoded in base64 since the backward slashes will
         * otherwise be removed before saving. The `setFunction` method
         * will decode the namespaces and forward all data.
         *
         * @see payment_rth_stripe::setFunction
         */
        $setFunctionField                 = self::class . '::setFunction(\'%s\',';
        $setFunctionFieldapiSandboxKey    = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxKey'));
        $setFunctionFieldapiSandboxSecret = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxSecret'));
        $setFunctionFieldapiLiveKey       = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveKey'));
        $setFunctionFieldapiLiveSecret    = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveSecret'));

        $this->addConfiguration('API_SANDBOX_KEY', 'sk_test_Y17KokhC3SRYCQTLYiU5ZCD2', 6, 1, $setFunctionFieldapiSandboxKey);
        $this->addConfiguration('API_SANDBOX_SECRET', 'pk_test_f3duw0VsAEM2TJFMtWQ90QAT', 6, 1, $setFunctionFieldapiSandboxSecret);
        $this->addConfiguration('API_LIVE_KEY', 'sk_Y17KokhC3SRYCQTLYiU5ZCD2', 6, 1, $setFunctionFieldapiLiveKey);
        $this->addConfiguration('API_LIVE_SECRET', 'pk_f3duw0VsAEM2TJFMtWQ90QAT', 6, 1, $setFunctionFieldapiLiveSecret);
    }

    public function remove(): void
    {
        parent::remove();

        foreach (self::$configurationKeys as $key) {
            $this->deleteConfiguration($key);
        }
    }

    protected function updateSteps(): int
    {
        $currentVersion = $this->getVersion();

        if (!$currentVersion) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }
}
