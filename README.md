# Module: Stripe payment module for modified shop
(DE): Modul: Stripe Zahnungsmodul für den modified shop

[![dicord](https://img.shields.io/discord/727190419158597683)](https://discord.gg/9NqwJqP)

🛠 This module is under development and not ready for use. You can make a contribution if you like.

## Installation
You can install this module with the [Modified Module Loader Client (MMLC)](http://module-loader.de).

Search for: `robinthehood/stripe`

## Requirements
PHP 8.0 or above

## Contributors
- Robin Wieschendorf | <mail@robinwieschendorf.de> | [robinwieschendorf.de](https://robinwieschendorf.de)
- [grandeljay](https://github.com/grandeljay)

## Contributing
We would be happy if you would like to take part in the development of this module. If you wish more features or you want to make improvements or to fix errors feel free to contribute. In order to contribute, you just have to fork this repository and make pull requests. If you don't know how to start, we are happy to help you on our [discord server](https://discord.gg/9NqwJqP).

### How to start
- We always try to document in the code why we do something. We hope this helps you to find your way around the code better.
- If you don't know exactly how to code something for modified, we've also written documentation for developers. Have a look: https://docs.module-loader.de
- You can discuss your idea with us on Discord. There is almost always someone online. Don't be afraid to write to us there.
- Opening an issue on GitHub.
- Or choose an issue you'd like to try. Let us know on the issue or on Discord that you're trying. This way we can help you and tasks are not processed twice.
- If you already have finished code, submit it as a PullRequest.
- Stripe has very good documentation. The module uses Stripe Checkout. The following website explains it clearly: https://stripe.com/docs/checkout/quickstart
- To test the Stripe module, you can create a (normal) free account with Stripe. This account is in sandbox (developer) mode. You will receive a public and private key from Stripe to test Stripe in sandbox mode.

### Coding Style
We are using:
- [PSR-1: Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/) with little changes. You can use only PSR-12 or see our ruleset.xml.

### How the module works

The module is a payment module that you as a shop owner can find under *Admin > Modules > Payment Modules*. This module is provided by class `payment_rth_stripe` in `src/includes/modules/payment/payment_rth_stripe.php`.

During the checkout process, this class uses the `selection()` method to ensure that the buyer sees Stripe when selecting the payment options.

In the order process step `checkout_confirmation.php`, the attribute `public $form_action_url = '/rth_stripe.php?action=checkout';` of the class `payment_rth_stripe` ensures that we go to our entry point file `rth_stripe.php` and not to `checkout_process.php > checkout_success.php` as it normally would.

In `rth_stripe.php` we first create a controller `/src-mmlc/Classes/Controller.php`. The `invokeCheckout()` method is then automatically called in the controller, since the URL contains `.../rth_stripe.php?action=checkout`. Our controller automatically calls the right method for us via the action query parameter.

In `invokeCheckout()` we create a strip checkout session using the strip lib (which we added to the project via composer) and redirect the buyer to stripe. When creating the stripe session, we can transmit the value of the shopping cart to Stripe and specify which pages Stripe should forward to if the payment was successful or unsuccessful.

The buyer can now make their payment on the Stripe checkout page. After that, Stripe will redirect us back to `.../rth_stripe.php?action=success` or to `.../rth_stripe.php?action=cancel`.

In the `invokeSuccess()` method of the controller we can now forward to `checkout_process.php > chekcout_success.php` so that the shop creates the order for us.

In `invokeCancel()` we can inform the buyer that the payment didn't work.

### Version and Commit-Messages
We are using:
- [Semantic Versioning 2.0.0](https://semver.org)
- [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)

## Support and Questions
You can ask your questions on our [discord server](https://discord.gg/9NqwJqP).
