<?php

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

use RobinTheHood\ModifiedStdModule\Classes\StdModule;

class rth_stripe extends StdModule
{
    public const VERSION = '0.1.0';

    public function __construct()
    {
        $this->init('MODULE_RTH_STRIPE');

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
