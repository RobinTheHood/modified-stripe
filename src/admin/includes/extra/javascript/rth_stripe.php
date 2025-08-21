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

if (rth_is_module_disabled('MODULE_PAYMENT_PAYMENT_RTH_STRIPE')) {
    return;
}

?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.rth-stripe-tabs');
        tabs.forEach(function(tab) {
            const navigationItems = tab.querySelectorAll('ul.navigation li');
            const contentItems = tab.querySelectorAll('ul.content li');

            // add a click event function for all tab headings
            navigationItems.forEach(function(item, itemIndex) {
                item.addEventListener('click', function () {
                    switchTab(itemIndex);
                });
            })

            const switchTab = function (tabIndex) {
                // set tab heading to active
                navigationItems.forEach(function (item) {
                    item.classList.remove("active");
                });
                navigationItems[tabIndex].classList.add("active");

                // set tab content to visible
                contentItems.forEach(function (item) {
                    item.style.display = 'none';
                });
                contentItems[tabIndex].style.display = 'block';
            };

            // set first tab to active after page load
            switchTab(0);
        });
    }, false);
</script>