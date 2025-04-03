# Changelog

All notable changes to `laravel-op` will be documented in this file

## 3.0.5 - 2025-04-02

- Add `db:seed ConfigurationSeeder` artisan comand to `post-create-project-cmd` script.

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/3.0.4...3.0.5

## 3.0.4 - 2025-04-02

- Add loader to load Constants dir
- Add Op constant file to define usefull constants.
- The default web route now renders the application info in json.
- Add dbconfig fileable collection.

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/3.0.3...3.0.4

## 3.0.3 - 2025-03-27

- feat: Add support for passing custom `emails` and `per_page` to SimpleDataExporter
  **Full Changelog**: https://github.com/toneflix/laravel-op/compare/3.0.2...3.0.3

## 3.0.2 - 2025-03-27

- feat: Allow exporting specific datasets only.

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/3.0.1...3.0.2

## 3.0.1 - 2025-03-27

- feat: Add Data Exporter.
- feat!: Rename Providers helper to Provider
- fix: Fix wrong Provider imports
- fix: Fix notification templates.
- feat: Ensure permission check for default admin controllers.
- fix: Remove Config Casts

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/3.0.0...3.0.1

## 3.0.0 - 2025-03-26

- Update dependencies to support laravel 12
- Update laravel/framework to ^12

## 2.1.0 - 2025-03-18

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.9...2.1.0

## 1.1.0 - 2025-03-18

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.9...1.1.0

## 2.0.9 - 2024-11-13

- Disable Payment feature tests.
- Remove extra redundant validateCsrfTokens middleware.
- Update Dependencies.

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.8...2.0.9

## 2.0.8 - 2024-09-21

- [Rename first_name to firstname in sync roles](https://github.com/toneflix/laravel-op/commit/1f9c28fa6e1281cbb283fa18744225f4cdfd728a)
- [Feat: Use ToneflixCode ResourceModifier for creating Api resources.](https://github.com/toneflix/laravel-op/commit/2072c0d82e2c816a01541cb462522f86ee2b071c)
- [Add roles and permissions to UserResource](https://github.com/toneflix/laravel-op/commit/832bce2446f3f5c0a834f6d1b115fe4e11371c53)
- [Add roles and permissions to UserResource](https://github.com/toneflix/laravel-op/commit/832bce2446f3f5c0a834f6d1b115fe4e11371c53)
- [Feat: Make it possible to overwrite message parser signature for each…](https://github.com/toneflix/laravel-op/commit/51c63c6679c93bc6bf03b429991b78c5f5f8819e)
- [Feat: Simplify route loading from the routes/* folders.](https://github.com/toneflix/laravel-op/commit/9a6a730eb55d51bbd7ceb2e3beefc061aa002e9d)
- [Refactor Configuration handling of file configs.](https://github.com/toneflix/laravel-op/commit/155cc084865795dcdc721cc5c7b8b0d6f99c799b)
- [Add textarea to ConfigType cast](https://github.com/toneflix/laravel-op/commit/50d6256f6f39e5575cb394b14ac7387773cb473b)
- [Add query logger to records SQL queries.](https://github.com/toneflix/laravel-op/commit/db258aca066c8ef2abef1d9646eae9e9465a8d03)
- [When genrating model policies, merge the exclude list with the default exclude list.](https://github.com/toneflix/laravel-op/commit/ce2bd8bf093e042ece93f8db0a4b60f3561dfa37)
- [Feat: MakePolicies command now discovers and makes policies for Models in sub dirs](https://github.com/toneflix/laravel-op/commit/d9ac395a58600a86228c449d39c52b4443a574c3)
- [Feat: Add $separator and $additional params to generateUsername ModelCanExtend trait method.
  ](https://github.com/toneflix/laravel-op/commit/57d9d8f1ad244336c68fc2cd1a63d831ce91497c)

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.7...2.0.8

## 2.0.7 - 2024-08-28

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.5...2.0.7

## 2.0.6 - 2024-08-18

* [Feat: Add a command to generate model policies for all models.](https://github.com/toneflix/laravel-op/commit/646e6d794dddee9698cf1de08a1e4b5c0a0233f2)
* Update: Refactored SyncRoles command.
* Updated conditional statements, and modified table outputs.
* [Feat: Add a command to generate model policies for all models.](https://github.com/toneflix/laravel-op/commit/646e6d794dddee9698cf1de08a1e4b5c0a0233f2).
* [Added 'admin_roles' configuration with a list of roles considered as admin](https://github.com/toneflix/laravel-op/commit/c8abd9dc61349519095db62cd4fef6432c363f63).
* [Added CSRF token validation middleware with exceptions for API routes](https://github.com/toneflix/laravel-op/commit/29654e23cd07166b97f7864c302742b03c8441cb).
* [Added secret value handling in ConfigValue cast](https://github.com/toneflix/laravel-op/commit/9c37a7291091794578c657b374ce84beeb4399c3).
* Fix: Updated SetConfig.php command to use Strings helper instead of Providers for JSON validation.
  **Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.5...2.0.6

## v2.0.5 - 2024-08-02

1. Add ability to verify account and reset password with token.
2. Add notifications for account verifications.
3. Add MessageParser class to improve typings.
4. Improve sendcode messages.
5. Add relevant default messages.
6. Add set-config artisan command.

**Full Changelog**: https://github.com/toneflix/laravel-op/compare/2.0.4...2.0.5

## 1.0.0 - 201X-XX-XX

- initial release
