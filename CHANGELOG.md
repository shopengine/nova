# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

This project does adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
## [feature/articlebundle]
### Changed
- Require shopengine/client-php feature/articlebundle

## [2.2.3] - 2022-11-09
### Changed
- Add fast like search for code and purchase
### Fixed
- Fix pagination
- Fix codepool code relation list

## [2.2.2] - 2022-06-24
### Fixed
- Show condition set name on nova detail page

## [2.2.1] - 2022-06-02
- Added tap and getCountForPagination implementation for Laravel Nova 3.30+

## [2.2.0] - 2022-04-13
### Changed
- Change SetOriginStatus to patch request
### Fixed
- ActionRequest input does not contain resource class

## [2.1.0] - 2022-04-06
### Added
- Add PurchaseOriginStatusFilter
- Show more fields for code and conditionset
- Implementation of CodepoolCodeMassAssign
- Sorting for ConditionSet
### Changed
- Display order date in CEST timezone

## [2.0.0] - 2022-03-15
### Added
- Added Hot Module loading (HMR)
- Merging of navigation
- Events
- Policies support for navigation
- Translations
- Compatibility with newer nova versions
- Compatibility with nova actions
- Structs
- Relationships for ShopEngine Models
- Navigation sorting
### Changed
- Update from laravel-mix 1.0 => 6.0
- Namespace shopengine/nova
### Fixed
- Pagination
- PSR
