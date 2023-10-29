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

if (rth_is_module_disabled(Classes\Constants::MODULE_PAYMENT_NAME)) {
    return;
}

?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.rth-stripe-tabs');
        tabs.forEach(function(tab) {
            // console.log(tab);

            const navigationItems = tab.querySelectorAll('ul.navigation li');
            const contentItems = tab.querySelectorAll('ul.content li');

            navigationItems[0].classList.add("active");

            contentItems.forEach(function (item) {
                item.style.display = 'none';
            });
            contentItems[0].style.display = 'block';

            navigationItems.forEach(function(item, itemIndex) {
                // console.log(item);

                item.addEventListener('click', function () {
                    navigationItems.forEach(function (item) {
                        item.classList.remove("active");
                    });

                    contentItems.forEach(function (item) {
                        item.style.display = 'none';
                    });

                    contentItems[itemIndex].style.display = "block";

                    // console.log(this);

                    this.classList.add("active");
                });

            })

        });

    }, false);
</script>