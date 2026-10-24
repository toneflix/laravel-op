# Laravel OP

[![Test & Lint](https://github.com/toneflix/laravel-op/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/toneflix/laravel-op/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/toneflix/laravel-op.svg)](https://packagist.org/packages/toneflix/laravel-op)
[![Total Downloads](https://img.shields.io/packagist/dt/toneflix/laravel-op.svg)](https://packagist.org/packages/toneflix/laravel-op)
[![License](https://img.shields.io/packagist/l/toneflix/laravel-op.svg)](https://packagist.org/packages/toneflix/laravel-op)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/toneflix/laravel-op/php)](https://packagist.org/packages/toneflix/laravel-op)
[![codecov](https://codecov.io/gh/toneflix/laravel-op/graph/badge.svg?token=2O7aFulQ9P)](https://codecov.io/gh/toneflix/laravel-op)

<!-- ![GitHub Actions](https://github.com/toneflix/laravel-op/actions/workflows/main.yml/badge.svg) -->

Laravel OP is a simple API agnostic fork of Laravel that provides a set of tools to help you build your Laravel application faster and easier. It is a collection of useful classes, traits, and functions that I have found useful in my own projects. It is a work in progress and I will be adding more features as I need them. I hope you find it useful too.

## Usage

To use this package you have to initialize it as a project using composer:

```bash
composer create-project toneflix/laravel-op
```

## What's Included

Laravel OP bundles a set of third-party packages and in-house subsystems chosen to cover the things almost every API backend ends up needing. Packages with their own documentation are linked out to rather than re-documented here.

### Payments

Three interchangeable payment processors behind a single `PaymentInterface` (`intent()` / `verify()` / `deauthorize()` / `transfer()`), selected per-request via `payWith($processor)` on `App\Traits\ControllerCanExtend`:

| Processor | Backed by | Notes |
|---|---|---|
| `paystack` | [`toneflix/paystack-php`](https://github.com/toneflix/paystack-php) | This kit's own Paystack SDK, replacing the abandoned `yabacon/paystack-php`. |
| `stripe` | [`stripe/stripe-php`](https://github.com/stripe/stripe-php) (official SDK) | Replaces the abandoned `cartalyst/stripe-laravel`. |
| `wallet` | [`toneflix/laravel-pay-pocket`](https://github.com/toneflix/laravel-pay-pocket) | Internal balance-based payments, no external gateway. Deducts synchronously from the user's wallet(s); see [Preparation](https://github.com/toneflix/laravel-pay-pocket#preparation) in that package's README for wallet types (`App\Enums\WalletEnums`) and priority order. |

Every attempt is recorded in the `Transaction` model (`App\Models\Transaction`), including a polymorphic `transactable` link back to whatever the payment was for.

### Notifications

- **Email & SMS verification**: 6-digit OTP codes (via `valorin/random`, not `rand()`), rate-limited, with a time-limited window (`token_lifespan` setting, in seconds). Also supports a base64url "code|hash" link variant for clickable email links.
- **SMS delivery** is pluggable between [`laravel-notification-channels/twilio`](https://github.com/laravel-notification-channels/twilio) and [`toneflix/kudisms-notification`](https://github.com/toneflix/kudisms-notification) (a Nigeria-market gateway), switchable at runtime via the `prefered_sms_channel` setting, no redeploy needed.
- **DB-editable templates**: every system email/SMS has a hardcoded default in `config/messages.php`, but an admin can override the copy per-key from the database via the admin API, without touching code.

### Settings

[`toneflix/laravel-dbconfig`](https://github.com/toneflix/laravel-dbconfig) is the backbone of runtime configuration: most settings a deployer would expect in `.env` (currency, token lifespans, reference prefixes, preferred SMS channel, feature toggles) are instead rows in the database, editable live through the admin `configurations` endpoint. `.env` is only the fallback for a handful of secrets.

### File uploads

[`toneflix/laravel-fileable`](https://github.com/toneflix/laravel-fileable) powers avatar uploads and named upload "collections" (banners, logos, private files/images/videos), responsive image variants, and secure/dynamic file-serving routes. Configured per-collection in `config/toneflix-fileable.php`.

### API responses & resources

[`toneflix/laravel-resource-modifier`](https://github.com/toneflix/laravel-resource-modifier) standardizes the shape of every JSON Resource response (pagination meta/links keys, optional camelCase) and ships a `make:resource` command this kit re-exposes under its own namespace. Every controller returns through a small set of response macros (`response()->success()/error()/info()`, see `App\Helpers\Provider`) for a consistent `{status, status_code, message, data}` envelope.

### Admin & permissions

[`spatie/laravel-permission`](https://github.com/spatie/laravel-permission) backs the whole authorization layer. Roles and permissions are declared in `config/permission-defs.php`; one Laravel Gate is registered per permission (`App\Helpers\Access`), with an `enable_admin_permission_middleware` setting to turn the whole thing off for local dev. `php artisan app:sync-roles` seeds roles/permissions onto users.

### Exports

Any model registered in `config/exports.php` can be exported to `.xlsx` via [`maatwebsite/excel`](https://github.com/SpartnerNL/Laravel-Excel), synchronously, or queued with an email notification linking to a time-limited signed download (`php artisan app:export`, or the generic `SimpleDataExporter` service).

### Sessions & devices

API auth is token-based via [`laravel/sanctum`](https://laravel.com/docs/sanctum), with each token named by the device's user-agent string. [`matomo/device-detector`](https://github.com/matomo-org/device-detector) turns that into a human-readable label for the "my devices" list (`GET /account/devices`), so a user can see and revoke sessions by device.

### Dev tooling

- [`toneflix/faker-pictwo`](https://github.com/toneflix/php-faker-pictwo) is bound as the app's global `Faker\Generator`, so any seeder/factory calling `fake()->imageUrl()` gets real-looking placeholder images out of the box.
- [`laravel/pint`](https://github.com/laravel/pint), [`laravel/sail`](https://github.com/laravel/sail), and the standard PHPUnit/Mockery/Collision stack for formatting, local Docker, and testing.
