# Hasura Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.2.1 - 2020-11-06

### Added

- Update namespaces to comply with psr-4 autoloading standard of Composer 2
- Use craft role (if available) instead of defaultRole if user is not an admin

### Fix

- fix bug that caused the auth action to fail if user does not exist
- fix preflight issue that was preventing local development

## 1.2.0 - 2020-09-24

### Added

- Add ability to write custom claims via twig
- Add default role to userGroup if available

## 1.1.0 - 2019-05-24

### Added

- Add docs for webhook

## 1.0.0 - 2019-03-15

### Added

- Initial release
