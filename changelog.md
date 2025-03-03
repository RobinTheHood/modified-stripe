# Changelog
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and uses [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
Unreleased features and fixes can be viewed on GitHub. To do this, click on [Unreleased].

## [0.7.0] - 2025-03-03
### Added
- Added multilingual support for payment title and description in the frontend, configurable through module settings [(#67)](https://github.com/RobinTheHood/modified-stripe/pull/67)

## [0.6.0] - 2025-03-01
### Added
- modified compatibility `3.1.0`, `3.1.1` and `3.1.2`

### Fixed
- Fixed an issue where PHP session expiration caused errors during checkout with the Stripe module due to time discrepancies between the database and PHP sessions. [(#64)](https://github.com/RobinTheHood/modified-stripe/pull/64)
- Fixed an issue where navigating back from the Stripe payment page or browser back button caused errors due to missing terms and conditions confirmation. [(#66)](https://github.com/RobinTheHood/modified-stripe/pull/66)

## [0.5.0] - 2024-02-05
### Added
- modified compatibility `3.0.1` and `3.0.2`

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
- modified compatibility `3.0.0`
- `checkout.session.expired` to install.md

## [0.1.1] - 2023-11-23
### Added
- module description to moduleinfo.json for mmlc version `<1.22.0`

### Fixed
- can not display stripe payment information of orders details with stripe payment

## [0.1.0] - 2023-11-23
### Added
- initial version

[Unreleased]: https://github.com/RobinTheHood/modified-stripe/compare/0.7.0...HEAD
[0.7.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.4.1...0.5.0
[0.4.1]: https://github.com/RobinTheHood/modified-stripe/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/RobinTheHood/modified-stripe/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/RobinTheHood/modified-stripe/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/RobinTheHood/modified-stripe/releases/tag/0.1.0