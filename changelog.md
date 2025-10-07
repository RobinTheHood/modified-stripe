# Changelog
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and uses [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
Unreleased features and fixes can be viewed on GitHub. To do this, click on [Unreleased].

## [0.15.0] - 2025-10-07
### Added
- Restrict Stripe payment availability by geo zone (tax class) via new "Payment Zone" config field. Stripe is only available if the billing address matches the selected zone.
- Restrict Stripe by allowed countries using the new "Allowed Countries" config field (comma-separated ISO codes, e.g. AT,DE). Core shop logic applies this automatically.

## [0.14.0] - 2025-09-02
### Added
- Support for modified version `3.1.5`
- Payout notification feature with email summaries for new Stripe payouts [(#93)](https://github.com/RobinTheHood/modified-stripe/pull/93)

## [0.13.0] - 2025-07-21
### Added
- Multi-language icon URL setting for payment selection description [(#80)](https://github.com/RobinTheHood/modified-stripe/pull/80)
- Add option to reset auto-increment order number after deleting temporary orders ([#84](https://github.com/RobinTheHood/modified-stripe/issues/84))
- Improved clarity for temporary Stripe orders in admin area ([#83](https://github.com/RobinTheHood/modified-stripe/issues/83))
- Visual indicators in order list to identify temporary orders with badges

## [0.12.0] - 2025-05-19
### Added
- Support for modified version `3.1.4`

## [0.11.0] - 2025-03-26
### Added
- Support for modified version `3.1.3`

## [0.10.0] - 2025-03-19
### Fixed
- Fixed a "CSRFToken is not defined" error when updating order status or entering a tracking number in the backend while the Admin Token System is enabled. [(#72)](https://github.com/RobinTheHood/modified-stripe/pull/72)
- Enabled deletion of `payment_rth_stripe` configuration key during the removal process to ensure proper cleanup.

### Changed
- Streamlined webhook action handling for improved maintainability and efficiency.  
- Enhanced configuration setup methods to ensure better flexibility and consistency.

## [0.9.0] - 2025-03-14
### Added
- Directly assign orders to their corresponding Stripe payments (PaymentIntents) upon successful checkout session completion, rather than waiting for Stripe webhook events [(#70)](https://github.com/RobinTheHood/modified-stripe/pull/70)
- Adds a button under Admin > Modules > Payment Options to automatically add, update, or remove the Stripe webhook endpoint [(#69)](https://github.com/RobinTheHood/modified-stripe/pull/69)

## [0.8.0] - 2025-03-13
### Added
- Manual Capture functionality with configurable setting in the backend [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Customizable payment status for authorized payments [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Display of more Stripe payment information in the admin order view [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Metadata attachment to CheckoutSession/PaymentIntent (OrderId, CustomerId, Customer Email) [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Ability to capture open payments with or without specifying amount [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Automatic order status updates after payment actions (capture, cancellation, refund) [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Payment cancellation functionality for authorized payments [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Refund functionality for captured payments [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)

### Changed
- Improved admin order display with two-column layout and AJAX loading [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Refactored order_detail architecture for better maintainability [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)
- Controller actions moved to dedicated services [(#68)](https://github.com/RobinTheHood/modified-stripe/pull/68)

### Thanks to our Sponsors
These features were made possible through the financial support of [antiquari.at](https://www.antiquari.at). Thank you!

## [0.7.0] - 2025-03-03
### Added
- Added multilingual support for payment title and description in the frontend, configurable through module settings [(#67)](https://github.com/RobinTheHood/modified-stripe/pull/67)

## [0.6.0] - 2025-03-01
### Added
- Support for modified version `3.1.0`, `3.1.1` and `3.1.2`

### Fixed
- Fixed an issue where PHP session expiration caused errors during checkout with the Stripe module due to time discrepancies between the database and PHP sessions. [(#64)](https://github.com/RobinTheHood/modified-stripe/pull/64)
- Fixed an issue where navigating back from the Stripe payment page or browser back button caused errors due to missing terms and conditions confirmation. [(#66)](https://github.com/RobinTheHood/modified-stripe/pull/66)

## [0.5.0] - 2024-02-05
### Added
- Support for modified version `3.0.1` and `3.0.2`

## [0.4.1] - 2024-01-06
You have to update the module via MMLC and under Admin > Modules > Payment Methods

### Fixed
- Fixed a bug that the module does not work with a shop installed in a subfolder. [(#57)](https://github.com/RobinTheHood/modified-stripe/pull/57)
- A bug has been fixed where only currencies in euros were transferred to Stripe. [(#58)](https://github.com/RobinTheHood/modified-stripe/pull/58)

## [0.4.0] - 2024-01-03
You have to update the module via MMLC and under Admin > Modules > Payment Methods

### Added
- A button has been added that allows you to automatically register the appropriate WebHook with Stripe. [(#51)](https://github.com/RobinTheHood/modified-stripe/pull/51)
- Changes to order status through Stripe events are now logged in the order history. [(#52)](https://github.com/RobinTheHood/modified-stripe/pull/52)

### Fixed
- An error has been fixed that can occur if the module tries to transmit an amount with decimal places to Stripe during the ordering process. [(#54)](https://github.com/RobinTheHood/modified-stripe/pull/54)

## [0.3.0] - 2023-12-28
You have to update the module via MMLC and under Admin > Modules > Payment Methods

### Added
- options to select order status via config for stripe webhook events [(#48)](https://github.com/RobinTheHood/modified-stripe/pull/48)

### Changed
- improve configuration text

## [0.2.0] - 2023-11-30
### Added
- Support for modified version `3.0.0`
- `checkout.session.expired` to install.md

## [0.1.1] - 2023-11-23
### Added
- module description to moduleinfo.json for mmlc version `<1.22.0`

### Fixed
- can not display stripe payment information of orders details with stripe payment

## [0.1.0] - 2023-11-23
### Added
- initial version

[Unreleased]: https://github.com/RobinTheHood/modified-stripe/compare/0.15.0...HEAD
[0.15.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.14.0...0.15.0
[0.14.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.13.0...0.14.0
[0.13.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.12.0...0.13.0
[0.12.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.11.0...0.12.0
[0.11.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.7.0...0.8.0
[0.7.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.4.1...0.5.0
[0.4.1]: https://github.com/RobinTheHood/modified-stripe/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/RobinTheHood/modified-stripe/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/RobinTheHood/modified-stripe/releases/tag/0.1.0