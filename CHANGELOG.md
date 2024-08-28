# Changelog

All notable changes to `laravel-op` will be documented in this file

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
