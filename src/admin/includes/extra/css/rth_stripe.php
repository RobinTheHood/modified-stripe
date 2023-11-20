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

namespace RobinTheHood\Stripe;

require_once DIR_FS_DOCUMENT_ROOT . '/includes/extra/functions/rth_modified_std_module.php';

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

if (
       !isset($_GET['module'], $_GET['action'])
    || \payment_rth_stripe::class !== $_GET['module']
    || 'edit'                     !== $_GET['action']
) {
    return;
}

$filename = 'includes/css/rth_stripe.css';
$version  = hash_file('crc32c', DIR_FS_ADMIN . $filename);
?>
<link rel="stylesheet" type="text/css" href="<?= DIR_WS_ADMIN . $filename ?>?v=<?= $version ?>" />
