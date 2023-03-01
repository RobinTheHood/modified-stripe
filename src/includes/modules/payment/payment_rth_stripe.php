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

    public function __construct()
    {
        parent::__construct(self::NAME);

        $this->checkForUpdate(true);

        foreach (self::$configurationKeys as $key) {
            $this->addKey($key);
        }
    }

    public function install()
    {
        parent::install();
    }

    public function remove()
    {
        parent::remove();

        foreach (self::$configurationKeys as $key) {
            $this->deleteConfiguration($key);
        }
    }

    protected function updateSteps()
    {
        $currentVersion = $this->getVersion();

        if (!$currentVersion) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }
}
