# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 12 + Filament 3 starter template with pre-configured authentication, user management, and role/permission management via **Filament Shield** (built on Spatie Laravel Permission). The admin panel lives at `/admin`.

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

**After adding a new Filament Resource** — regenerate Shield permissions and policies:
```bash
php artisan shield:generate --all
```

## Architecture

### Filament Panel
Configured in `app/Providers/Filament/AdminPanelProvider.php`. Uses auto-discovery for resources, pages, and widgets under `app/Filament/`. Resources are placed in `app/Filament/Resources/`, pages in `app/Filament/Pages/`.

### Permissions System (Shield)
- Roles and permissions use **Spatie Laravel Permission** via `HasRoles` on the `User` model.
- **Filament Shield** auto-generates permission strings (e.g. `view_any_user`, `create_user`) and corresponding policies when you run `shield:generate`.
- The `super_admin` role bypasses all permission checks (Gate intercept set to `before`).
- Policies live in `app/Policies/` and check `$user->can('action_resource')` strings — do not add custom logic there; update Shield config instead.
- `RoleResource` (`app/Filament/Resources/RoleResource.php`) extends `ShieldRoleResource` only to override the navigation group to `Seguridad`.

### User Model & `activo` Field
`User` has a boolean `activo` column (default `true`) that controls account access. The custom `Login` page (`app/Filament/Pages/Auth/Login.php`) overrides Filament's base login to log out and show a danger notification when a user with `activo = false` tries to authenticate. The `User` model implements `MustVerifyEmail`.

### Navigation Groups
All security-related resources (Usuarios, Roles) are grouped under `'Seguridad'` in the sidebar.

### Testing
Tests use **Pest** with SQLite in-memory (configured in `phpunit.xml`). Feature tests are in `tests/Feature/`, unit tests in `tests/Unit/`.

### UI Labels
All user-facing labels in Filament forms and tables are in **Spanish** (`'Nombre'`, `'Correo electrónico'`, etc.). Follow this convention when adding new resources.
