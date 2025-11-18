# Troubleshooting Index

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document provides a centralized troubleshooting guide with links to detailed solutions in the specific documentation files.

## 2 PHP Runtime Issues

### 2.1 PHP Version Not Found

**Symptom**: `php: command not found` or wrong PHP version

**Solutions**:
\* See [php-runtime.md](030-php-runtime.md) for installation methods
\* Use Herd (macOS), Homebrew, or Laravel Sail
\* Verify with `php -v`

### 2.2 Missing PHP Extensions

**Symptom**: Composer install fails with missing extension errors

**Solutions**:
\* See [02-php-runtime.md - Required Extensions](030-php-runtime.md#required-php-extensions)
\* Install missing extensions via package manager
\* For Herd: Extensions are usually pre-installed

## 3 Laravel Core Issues

### 3.1 Application Key Not Set

**Symptom**: Encryption errors, session issues

**Solution**: Run `php artisan key:generate`

### 3.2 Configuration Cache Issues

**Symptom**: Changes to `.env` not reflected

**Solution**: Run `php artisan config:clear`

### 3.3 Route Not Found

**Symptom**: 404 errors on known routes

**Solutions**:
\* Clear route cache: `php artisan route:clear`
\* Verify routes: `php artisan route:list`
\* Check `bootstrap/app.php` for route configuration

See [03-laravel-core.md - Troubleshooting](040-laravel-core.md#troubleshooting) for more.

## 4 Database Issues

### 4.1 SQLite Database Not Found

**Symptom**: `SQLite database not found` error

**Solution**:

``` bash
touch database/database.sqlite
php artisan migrate
```

### 4.2 WAL Mode Not Working

**Symptom**: Concurrent access issues with SQLite

**Solution**: See [040-laravel-core.md - SQLite WAL Mode](040-laravel-core.md#sqlite-wal-mode-configuration) for configuration

### 4.3 Migration Errors

**Symptom**: Migration fails or tables already exist

**Solutions**:
\* Check migration status: `php artisan migrate:status`
\* Rollback if needed: `php artisan migrate:rollback`
\* Fresh start (development only): `php artisan migrate:fresh`

## 5 Frontend Build Issues

### 5.1 Assets Not Loading

**Symptom**: CSS/JS files return 404

**Solutions**:
\* Rebuild assets: `bun run build`
\* Start dev server: `bun run dev`
\* Check Vite manifest: `public/build/.vite/manifest.json`

See [130-frontend-build.md - Troubleshooting](130-frontend-build.md#troubleshooting) for more.

### 5.2 Using npm/npx Commands

**Symptom**: Lock file conflicts, dependency issues

**Solution**: See [12-frontend-build.md - Using npm/npx Commands](130-frontend-build.md#issue-using-npm-npx-commands)

**Quick fix**:

``` bash
rm -rf node_modules package-lock.json yarn.lock
bun install
```

### 5.3 Tailwind Classes Not Working

**Symptom**: Tailwind utilities not applying

**Solutions**:
\* Verify CSS import in `resources/css/app.css`
\* Rebuild assets: `bun run build`
\* Check Tailwind CSS 4 configuration (no config file needed)

See [12-frontend-build.md - Tailwind Issues](130-frontend-build.md#issue-tailwind-classes-not-working)

## 6 Package Installation Issues

### 6.1 Composer Dependency Conflicts

**Symptom**: Composer install fails with dependency conflicts

**Solutions**:
\* Update Composer: `composer self-update`
\* Clear cache: `composer clear-cache`
\* Remove `composer.lock` and reinstall (development only)
\* Check PHP version compatibility

### 6.1.1 Livewire ComponentRegistry Error

**Symptom**: `BindingResolutionException: Target class [Livewire\Mechanisms\ComponentRegistry] does not exist`

**Cause**: Filament v5 beta compatibility issue with Livewire v4.0.0-beta.3. The `ComponentRegistry` class was removed in Livewire v4.

**Solution**: This project includes a composer patch that automatically fixes this issue. The patch is located at:

``` bash
patches/filament-filament/livewire-v4-compatibility.patch
```

**Verification**:

``` bash
# Verify patch is configured in composer.json
grep -A 2 "filament/filament" composer.json

# Run composer update to apply patches
composer update -Wo

# Verify application boots correctly
php artisan --version
```

**What the patch does**: Replaces `app(ComponentRegistry::class)->getName($component)` with `app('livewire.factory')->resolveComponentName($component)` in Filament's `HasComponents` trait.

See [patches/filament-filament/README.md](../../patches/filament-filament/README.md) for details.

### 6.2 Flux Pro Authentication Failed

**Symptom**: Cannot install Flux Pro packages

**Solutions**:
\* See [04-livewire-ecosystem.md - Authentication](050-livewire-ecosystem.md#authentication-configuration)
\* Verify `auth.json` exists and has correct credentials
\* Check `FLUX_PRO_USERNAME` and `FLUX_PRO_PASSWORD` in `.env`
\* Ensure `auth.json` is gitignored

### 6.3 Development Package Updates

**Symptom**: Breaking changes after updating dev packages

**Solution**: See [README.md - Package Update Strategy](README.md#package-update-strategy)

**Best practice**: Update one package at a time and test thoroughly.

## 7 Testing Issues

### 7.1 Pest Tests Not Running

**Symptom**: `pest: command not found` or tests not executing

**Solutions**:
\* Verify Pest installation: `composer show pestphp/pest`
\* Run via Artisan: `php artisan test`
\* Check test directory structure: `tests/Feature/` and `tests/Unit/`

See [11-development-tools.md - Pest 4](120-development-tools.md#pest-4) for details.

### 7.2 Playwright Browsers Not Found

**Symptom**: Browser tests fail with "browser not found"

**Solution**:

``` bash
bunx playwright install --with-deps
```

See [11-development-tools.md - Browser Testing](120-development-tools.md#browser-testing-playwright) for more.

### 7.3 Database in Tests

**Symptom**: Tests affecting each other's data

**Solution**: Use `RefreshDatabase` trait or database transactions in tests

### 7.4 Code Coverage Issues

**Symptom**: Coverage tool reports lines as uncovered even though tests pass

**Solutions**:
\* See [Setup Notes - VoltServiceProvider Coverage Issue](800-notes-and-queries.md#27-voltserviceprovider-code-coverage-issue---line-71-not-covered) for detailed solution
\* Add explicit assertions within mock callbacks to verify return values
\* Use `andReturnUsing()` with debugging assertions when coverage tool has difficulty detecting statement execution
\* Check HTML coverage reports to identify which specific lines are not covered

## 8 Authentication Issues

### 8.1 Flux Pro Repository Access

**Symptom**: Cannot access Flux Pro repository

**Solutions**:
\* Verify credentials in `auth.json`
\* Check environment variables
\* Ensure repository URL is correct in `composer.json`

See [04-livewire-ecosystem.md - Authentication Configuration](050-livewire-ecosystem.md#authentication-configuration)

### 8.2 Custom Repository Authentication

**Symptom**: Composer authentication errors

**Solution**: See [13-outstanding-questions.md - Custom Repository Auth](140-outstanding-questions.md#custom-repository-authentication)

## 9 Navigation

[← Outstanding Questions, Decisions, and Inconsistencies](140-outstanding-questions.md) | [↑ Top](#troubleshooting-index) | [Setup Notes and Queries →](800-notes-and-queries.md)
