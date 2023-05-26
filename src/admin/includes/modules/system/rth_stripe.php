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
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */

use RobinTheHood\ModifiedStdModule\Classes\StdModule;
use RobinTheHood\Stripe\Classes\Constants;

class rth_stripe extends StdModule
{
    public const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct(Constants::MODULE_NAME);

        $this->checkForUpdate(true);
    }

    public function display()
    {
        return $this->displaySaveButton();
    }

    public function install()
    {
        parent::install();
    }

    public function remove()
    {
        parent::remove();
    }

    protected function updateSteps()
    {
        $currentVersion = $this->getVersion();

        if (!$currentVersion) {
            $this->setVersion('0.1.0');

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }
}
