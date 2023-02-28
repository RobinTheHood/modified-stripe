<?php

use RobinTheHood\ModifiedStdModule\Classes\StdModule;

/**
 * You can find informations about payment classes and development at:
 * https://docs.module-loader.de
 */
class payment_rth_stripe extends StdModule
{
    public function __construct()
    {
        parent::__construct('MODULE_PAYMENT_RTH_STRIPE');

        $this->checkForUpdate(true);
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
