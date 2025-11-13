# Project Overview and Architecture

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 MD033 -->

## 1 Introduction

This document provides a comprehensive overview of the Laravel Livewire Starter Kit project, including its architecture, package ecosystem, and design principles.

## 2 Project Overview

The Laravel Livewire Starter Kit is the official starter kit for building Laravel applications with Livewire. It provides a modern, type-safe foundation with comprehensive tooling for development, testing, and deployment.

### 2.1 Key Characteristics

- **Framework**: Laravel 12.x
- **Frontend**: Livewire 4 with Volt (Single File Components)
- **PHP Version**: 8.4+ (8.4.12 recommended)
- **Package Manager**: Bun (exclusive - do not use npm, npx, or yarn)
- **Styling**: Tailwind CSS 4
- **Testing**: Pest 4 with browser testing capabilities
- **Type Safety**: PHPStan Level 10 (max) compliance required

### 2.2 Project Structure

The project follows Laravel 12’s streamlined structure:

- `bootstrap/app.php` - Application bootstrap and configuration
- `bootstrap/providers.php` - Service provider registration
- `app/` - Application code following PSR-4 autoloading
- `routes/` - Route definitions
- `resources/` - Views, assets, and frontend resources
- `tests/` - Test suite organized by type (Feature, Unit, Browser, etc.)

## 3 Package Ecosystem Overview

### 3.1 Production Dependencies (34 packages + PHP runtime)

<table>
<colgroup>
<col style="width: 18%" />
<col style="width: 54%" />
<col style="width: 27%" />
</colgroup>
<thead>
<tr>
<th style="text-align: left;">Category</th>
<th style="text-align: left;">Packages</th>
<th style="text-align: left;">Notes</th>
</tr>
</thead>
<tbody>
<tr>
<td style="text-align: left;"><p>PHP Runtime</p></td>
<td style="text-align: left;"><p><code>php (^8.4)</code></p></td>
<td style="text-align: left;"><p>Minimum supported runtime (8.4.12 recommended)</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Laravel Foundation &amp; Runtime (5)</p></td>
<td style="text-align: left;"><p><code>laravel/framework (^12.36)</code><br />
<code>laravel/tinker (^2.10)</code><br />
<code>laravel/mcp (^0.3)</code><br />
<code>runtime/frankenphp-symfony (^0.2.0)</code><br />
<code>cweagans/composer-patches (^1.7)</code></p></td>
<td style="text-align: left;"><p>Core framework, REPL, MCP server integration, high-performance runtime, Composer patch utility</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Livewire &amp; UI Stack (5)</p></td>
<td style="text-align: left;"><p><code>livewire/livewire (^4.0)</code><br />
<code>livewire/volt (dev-main)</code><br />
<code>livewire/flux (^2.6)</code><br />
<code>livewire/flux-pro (^2.6)</code><br />
<code>filament/filament (^5.x-dev)</code></p></td>
<td style="text-align: left;"><p>Reactive components, Volt SFCs, Flux UI library, Filament admin panel</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Authentication &amp; Identity (3)</p></td>
<td style="text-align: left;"><p><code>laravel/fortify (^1.31)</code><br />
<code>laravel/sanctum (^4.0)</code><br />
<code>laravel/workos (^0.5)</code></p></td>
<td style="text-align: left;"><p>Authentication scaffolding, API token auth, enterprise SSO integration</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Routing, Performance &amp; Queues (3)</p></td>
<td style="text-align: left;"><p><code>laravel/folio (dev-master)</code><br />
<code>laravel/octane (^2.13)</code><br />
<code>laravel/horizon (^5.38)</code></p></td>
<td style="text-align: left;"><p>File-based routing, high-performance runtime server, queue dashboard</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Realtime &amp; Messaging (3)</p></td>
<td style="text-align: left;"><p><code>laravel/reverb (^1.0)</code><br />
<code>ably/ably-php (^1.1)</code><br />
<code>ably/laravel-broadcaster (^1.0)</code></p></td>
<td style="text-align: left;"><p>WebSocket server, Ably PHP SDK, Ably broadcaster bridge</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Observability &amp; Logging (4)</p></td>
<td style="text-align: left;"><p><code>laravel/telescope (^5.15)</code><br />
<code>spatie/laravel-activitylog (^4.10)</code><br />
<code>spatie/laravel-analytics (^5.6)</code><br />
<code>monolog/monolog (dev-main)</code></p></td>
<td style="text-align: left;"><p>Application observability, activity auditing, analytics reporting, logging stack</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Search &amp; Discovery (2)</p></td>
<td style="text-align: left;"><p><code>laravel/scout (^10.20)</code><br />
<code>typesense/typesense-php (^5.2.0-RC4)</code></p></td>
<td style="text-align: left;"><p>Full-text search abstraction with Typesense driver</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Data Lifecycle &amp; Content (9)</p></td>
<td style="text-align: left;"><p><code>askedio/laravel-soft-cascade (^12.0)</code><br />
<code>spatie/laravel-backup (^9.3)</code><br />
<code>spatie/laravel-deleted-models (^1.1)</code><br />
<code>spatie/laravel-event-sourcing (^7.12)</code><br />
<code>spatie/laravel-health (^1.34)</code><br />
<code>spatie/laravel-markdown (^2.7)</code><br />
<code>spatie/laravel-medialibrary (^11.17)</code><br />
<code>spatie/laravel-schedule-monitor (^4.1)</code><br />
<code>spatie/laravel-settings (^3.5)</code></p></td>
<td style="text-align: left;"><p>Backups, soft delete cascade, event sourcing, health checks, markdown rendering, media management, schedule monitoring, application settings</p></td>
</tr>
</tbody>
</table>

### 3.2 Development Dependencies (40 packages)

<table>
<colgroup>
<col style="width: 18%" />
<col style="width: 54%" />
<col style="width: 27%" />
</colgroup>
<thead>
<tr>
<th style="text-align: left;">Category</th>
<th style="text-align: left;">Packages</th>
<th style="text-align: left;">Notes</th>
</tr>
</thead>
<tbody>
<tr>
<td style="text-align: left;"><p>Testing &amp; Quality Assurance (13)</p></td>
<td style="text-align: left;"><p><code>pestphp/pest (^4.1)</code><br />
<code>pestphp/pest-plugin-arch (^4.0)</code><br />
<code>pestphp/pest-plugin-browser (^4.1)</code><br />
<code>pestphp/pest-plugin-faker (^4.0)</code><br />
<code>pestphp/pest-plugin-laravel (^4.0)</code><br />
<code>pestphp/pest-plugin-profanity (^4.2)</code><br />
<code>pestphp/pest-plugin-type-coverage (^4.0)</code><br />
<code>laravel-labs/starter-kit-browser-tests (dev-main)</code><br />
<code>phpunit/phpunit (^12.4)</code><br />
<code>mockery/mockery (^1.6)</code><br />
<code>fakerphp/faker (^1.24)</code><br />
<code>jasonmccreary/laravel-test-assertions (^2.8)</code><br />
<code>spatie/pest-plugin-snapshots (^2.2)</code></p></td>
<td style="text-align: left;"><p>Primary testing framework, browser automation, fixtures, assertion helpers, snapshot testing</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Static Analysis &amp; Refactoring (9)</p></td>
<td style="text-align: left;"><p><code>larastan/larastan (^3.8)</code><br />
<code>phpstan/phpstan (^2.1.32)</code><br />
<code>phpstan/phpstan-deprecation-rules (^2.0)</code><br />
<code>phpstan/phpstan-phpunit (^2.0)</code><br />
<code>phpstan/phpstan-strict-rules (^2.0)</code><br />
<code>phpstan/extension-installer (^1.3)</code><br />
<code>rector/rector (^2.2)</code><br />
<code>driftingly/rector-laravel (^2.1)</code><br />
<code>rector/type-perfect (^2.1)</code></p></td>
<td style="text-align: left;"><p>Static analysis (Level 10), deprecation scanning, PHPUnit integration, automated refactoring</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Developer Workflow &amp; Tooling (7)</p></td>
<td style="text-align: left;"><p><code>laravel/boost (^1.6)</code><br />
<code>laravel/pail (^1.2)</code><br />
<code>laravel/pint (^1.25)</code><br />
<code>laravel/sail (^1.47)</code><br />
<code>laravel-shift/blueprint (^2.12)</code><br />
<code>soloterm/solo (^0.5.0)</code><br />
<code>ergebnis/composer-normalize (^2.48)</code></p></td>
<td style="text-align: left;"><p>Local workflow automation, log streaming, code formatting, Sail Docker environment, scaffolding, Composer hygiene</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Debugging &amp; Developer Experience (5)</p></td>
<td style="text-align: left;"><p><code>barryvdh/laravel-debugbar (^3.13)</code><br />
<code>barryvdh/laravel-ide-helper (^3.6)</code><br />
<code>nunomaduro/collision (^8.8)</code><br />
<code>spatie/laravel-ray (^1.41)</code><br />
<code>spatie/laravel-web-tinker (^1.10)</code></p></td>
<td style="text-align: left;"><p>Runtime debugging, IDE autocompletion, exception rendering, Ray debugger, in-browser Tinker</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Spatie Support Utilities (5)</p></td>
<td style="text-align: left;"><p><code>spatie/laravel-blade-comments (^2.0)</code><br />
<code>spatie/laravel-horizon-watcher (^1.1)</code><br />
<code>spatie/laravel-login-link (^1.6)</code><br />
<code>spatie/laravel-missing-page-redirector (^2.11)</code><br />
<code>spatie/laravel-queueable-action (^2.16)</code></p></td>
<td style="text-align: left;"><p>Blade comment cleanup, Horizon monitoring, passwordless login links, 404 redirects, queueable action helpers</p></td>
</tr>
<tr>
<td style="text-align: left;"><p>Security &amp; Compliance (1)</p></td>
<td style="text-align: left;"><p><code>roave/security-advisories (dev-latest)</code></p></td>
<td style="text-align: left;"><p>Locks known vulnerable packages during development</p></td>
</tr>
</tbody>
</table>

### 3.3 Frontend Dependencies

The Bun workspace is defined in `package.json` with the following requirements and tooling:

- **Runtime Requirements**: Node `>=25`, Bun `>=1.1.0` (enforced via the `"engines"` field)
- **Build & Bundling**: `vite (^7.2.2)`, `rolldown-vite (^7.2.4)`, `@tailwindcss/vite (^4.1.17)`, `laravel-vite-plugin (^2.0.1)`
- **Styling**: `tailwindcss (^4.1.17)`, `autoprefixer (^10.4.22)`
- **Realtime Client Support**: `ably (^2.14.0)`, `laravel-echo (^2.2.6)`, `pusher-js (^8.4.0)`
- **HTTP & Utilities**: `axios (^1.13.2)`, `concurrently (^9.2.1)`, `bun-git-hooks (^0.3.1)`, `npm-check-updates (^19.1.2)`
- **Code Quality**: `prettier (^3.6.2)` with `prettier-plugin-tailwindcss (^0.7.1)`, `prettier-plugin-organize-imports (^4.3.0)`, and `prettier-plugin-packagejson (^2.5.19)`
- **Testing**: `playwright (^1.56.1)`, `vitest (^4.0.8)`
- **Optional Native Binaries**: Platform-specific Rollup, Tailwind Oxide, and Lightning CSS packages for faster local builds (installed automatically when available)

## 4 Architectural Patterns

### 4.1 Domain-Driven Design

The application follows Domain-Driven Design principles with clear layer separation:

- **Application Layer**: Business logic and use cases
- **Domain Layer**: Core business entities and value objects
- **Infrastructure Layer**: External integrations and implementations
- **Presentation Layer**: UI components and user interactions

### 4.2 Component Architecture

- **Livewire Components**: Server-side rendered interactive components
- **Volt Single File Components**: Simplified component syntax combining PHP and Blade
- **Flux UI Components**: Pre-built, accessible UI component library
- **Blade Templates**: Server-side templating with Livewire integration

### 4.3 State Management

- **State Machines**: Spatie Laravel Model States for complex state transitions
- **Model Status**: Spatie Laravel Model Status for status tracking
- **Feature Flags**: Spatie Laravel Model Flags for feature toggles

## 5 Design Principles

### 5.1 Type Safety

- **PHPStan Level 10**: Maximum strictness for static analysis
- **Strict Types**: All PHP files use `declare(strict_types=1)`
- **Type Coverage**: 100% type coverage target for tests
- **Explicit Return Types**: All methods must have explicit return types

### 5.2 Code Quality

- **Laravel Pint**: Automated code formatting (PSR-12)
- **Rector**: Automated code refactoring and modernization
- **PHPStan**: Static analysis at maximum level
- **Type Perfect**: Additional type checking for Rector

### 5.3 Testing Standards

- **Pest 4**: Modern testing framework with browser testing
- **100% Coverage**: Target for unit tests
- **Browser Testing**: Real browser testing with Playwright
- **Type Coverage**: 100% type coverage requirement

## 6 Integration Points

### 6.1 Laravel Integration

All packages integrate with Laravel’s service container:

- **Service Providers**: Auto-discovery enabled for most packages
- **Facades**: Available where appropriate
- **Events**: Event-driven architecture throughout
- **Middleware**: Request/response processing
- **Queues**: Background job processing with Horizon

### 6.2 Frontend Integration

- **Bun**: Fast JavaScript runtime and package manager (exclusive - use `bun` and `bunx`, never `npm`/`npx`/`yarn`)
- **Vite**: Modern build tool with HMR (Hot Module Replacement)
- **Livewire**: Server-side components with client-side interactivity
- **Tailwind CSS 4**: Utility-first CSS framework

## 7 Custom Repositories

The project uses two custom Composer repositories:

- **Flux Pro**: `https://composer.fluxui.dev` - Commercial Flux UI components
- **Laravel Comments**: `https://satis.spatie.be` - Spatie’s private packages

## 8 Version Constraints

The project uses a hybrid approach to version constraints:

### 8.1 Core Packages (Stable)

Core packages use stable versions for stability:
\* `laravel/framework: ^12.36` - Stable Laravel 12
\* `livewire/livewire: ^4.0` - Stable Livewire 4

### 8.2 Development Packages

Some newer packages use development versions for cutting-edge features:
\* `filament/filament: ^5.x-dev` - Development version of Filament 5
\* `laravel/folio: dev-master` - Development version of Folio
\* `livewire/volt: dev-main` - Development version of Volt
\* `roave/security-advisories: dev-latest` - Latest security advisories

This hybrid approach balances stability for core packages with access to latest features for newer packages.

## 9 Next Steps

After reading this overview, proceed to:

- [php-runtime.md](030-php-runtime.md) - Set up PHP 8.4+ runtime
- [laravel-core.md](040-laravel-core.md) - Configure Laravel Framework 12.x

## 10 Navigation

[← Quick Start Guide](010-quick-start.md) | [↑ Top](#project-overview-and-architecture) | [PHP Runtime Setup →](030-php-runtime.md)
