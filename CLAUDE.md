# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 13 + Filament 5 starter template with pre-configured authentication, user management, and role/permission management via **Filament Shield v4** (built on Spatie Laravel Permission). The admin panel lives at `/admin`.

## Commands

**First-time setup** (after cloning and configuring `.env`):
```bash
php artisan app:install-template
npm install && npm run build
```
This runs: `key:generate`, `migrate`, `make:filament-user`, `shield:super-admin`, `shield:generate --all`.

**Development server** (runs Laravel + queue + Vite concurrently):
```bash
composer run dev
```

**Run all tests:**
```bash
composer test
```

**Run a single test file:**
```bash
php artisan test tests/Feature/ExampleTest.php
```

**Run a specific test by name:**
```bash
php artisan test --filter=test_name
```

**Code style (Laravel Pint):**
```bash
./vendor/bin/pint
```

**Static analysis (Larastan/PHPStan level 5):**
```bash
./vendor/bin/phpstan analyse
```

**Generate IDE helper files:**
```bash
php artisan ide-helper:generate
php artisan ide-helper:models --nowrite
```

**After adding a new Filament Resource** — regenerate Shield permissions and policies:
```bash
php artisan shield:generate --all
```

## Architecture

### Filament Panel (Filament 5)
Configured in `app/Providers/Filament/AdminPanelProvider.php`. Uses auto-discovery for resources, pages, and widgets under `app/Filament/`. Filament 5 replaced the `Form` class with `Schema`: resource `form()` methods now take `Filament\Schemas\Schema` and return `Schema`, using `->components([...])` instead of `->schema([...])`. Layout components (`Grid`, `Section`) moved from `Filament\Forms\Components` to `Filament\Schemas\Components`. Field components (`TextInput`, `Toggle`, `CheckboxList`) remain in `Filament\Forms\Components`.

Navigation property types changed in Filament 5: `$navigationGroup` must be `string|\UnitEnum|null` and `$navigationIcon` must be `string|\BackedEnum|null` (not `?string`).

### Permissions System (Shield v4)
- Roles and permissions use **Spatie Laravel Permission** via `HasRoles` on the `User` model. `PermissionServiceProvider` auto-discovered via Composer — not manually registered in `bootstrap/providers.php`.
- **Filament Shield v4** auto-generates permissions with format `Action:Resource` (separator `:`, case `pascal`), and corresponding policies when you run `shield:generate`. Shield v4's `RoleResource` is at namespace `BezhanSalleh\FilamentShield\Resources\Roles\RoleResource` (changed from v3's `BezhanSalleh\FilamentShield\Resources\RoleResource`).
- The `super_admin` role bypasses all permission checks (Gate intercept `before`).
- Policies in `app/Policies/` check `$user->can('action_resource')` strings.
- `RoleResource` (`app/Filament/Resources/RoleResource.php`) extends `ShieldRoleResource` only to override the navigation group to `Seguridad`.

### User Model — `canAccessPanel`
`User` implements `FilamentUser` with `canAccessPanel(Panel $panel): bool` that returns `(bool) $this->activo`. Filament calls this after authentication; users with `activo = false` are blocked at the framework level. No custom Login page is used.

### Activity Log
`spatie/laravel-activitylog` is installed. Migrations are published to `database/migrations/`. Use `activity()->log('message')` or `$model->activity()` for model-level logging.

### Telescope
Registered **only in local environment** via `AppServiceProvider::register()`. Prevented from auto-discovery in `composer.json` (`dont-discover`). Access at `/telescope`. Not loaded in production.

### Laravel Lang
Spanish translations published to `lang/es/`. To add more languages: `php artisan lang:add <locale>`.

### Testing
Tests use **Pest v4** with SQLite in-memory (configured in `phpunit.xml`). Feature tests in `tests/Feature/`, unit tests in `tests/Unit/`.

### UI Labels
All user-facing labels in Filament forms and tables are in **Spanish** (`'Nombre'`, `'Correo electrónico'`, etc.). Follow this convention when adding new resources.
