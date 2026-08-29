# eighteen73 Laravel Toolkit

A central package for developer tooling, AI assist agents, shared testing conventions, and architecture standards across eighteen73's Laravel projects.

## Installation

You can install the package via Composer as a development dependency:

```bash
composer require --dev eighteen73/laravel-toolkit
```

The package will automatically register its service provider `Eighteen73\Toolkit\ToolkitServiceProvider` using Laravel's package discovery.

## Features

### Testing Standards

The package provides shared Pest testing conventions built on Pest 3's `arch()->preset()->laravel()` and `arch()->preset()->security()` presets alongside eighteen73 conventions and `composer.json` verification.

When registered, it automatically enforces:
- Full Laravel framework architecture conventions via `arch()->preset()->laravel()` (controllers, models, commands, requests, jobs, mailables, notifications, providers, listeners, middleware, exceptions, and policies).
- Insecure function bans via `arch()->preset()->security()` (`eval`, `exec`, `shell_exec`, `system`, `unserialize`, `md5`, `sha1`, `rand`, `uniqid`, etc.).
- Banning of all leftover debugging functions (`dd`, `dump`, `ray`, and `var_dump`) in CI mode.
- Observer suffix conventions (`App\Observers` ending in `Observer`).
- Validation rule conventions (`App\Rules` implementing `Illuminate\Contracts\Validation\ValidationRule`).
- Agency `composer.json` compliance (`ergebnis/composer-normalize` present and allowed, `config.platform.php` defined, and platform PHP version satisfying the `require.php` constraint).

In your parent application's `tests/Arch.php` (or `tests/Feature/ArchTest.php`):

```php
use Eighteen73\Toolkit\Testing\AgencyStandards;

AgencyStandards::register();
```

You can pass project-specific ignores as a list of classes / functions or grouped by category (e.g. allowing `md5` for Gravatars or checksums):

```php
AgencyStandards::register(
    ignores: [
        'App\Models\LegacyData',
        'security' => [
            'md5',
        ],
    ]
);
```

### Laravel Boost AI Agents

When `laravel/boost` is installed, the package automatically registers eighteen73 custom AI agents (such as the Gemini CLI agent configured for eighteen73 project guidelines and MCP servers).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
