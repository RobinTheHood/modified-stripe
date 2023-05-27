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
 * @phpcs:disable PSR1.Files.SideEffects
 */

use RobinTheHood\Stripe\Classes\Constants;

$prefix = Constants::MODULE_SYSTEM_NAME . '_';

define($prefix . 'TITLE', 'Stripe Paymentmodule © by <a href="https://github.com/RobinTheHood/modified-stripe" target="_blank" style="font-weight: bold">RobinTheHood, grandeljay</a>');
define($prefix . 'LONG_DESCRIPTION', 'A modified-shop module that allows payments via Stripe.');
define($prefix . 'STATUS_TITLE', 'robinthehood/stripe Modul active?');
define($prefix . 'STATUS_DESC', '');
