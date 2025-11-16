# Setup Documentation

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

---

<details>
<summary>Expand for Table of Contents</summary>

- [Setup Documentation](#setup-documentation)
  - [1 Introduction](#1-introduction)
    - [1.1 Documentation Standards](#11-documentation-standards)
    - [1.2 Official Documentation Sources](#12-official-documentation-sources)
  - [2 Setup Order and Workflow](#2-setup-order-and-workflow)
    - [2.1 Recommended Setup Order](#21-recommended-setup-order)
    - [2.2 Quick Setup Commands](#22-quick-setup-commands)
    - [2.3 Verification Checklist](#23-verification-checklist)
  - [3 Documentation Structure](#3-documentation-structure)
  - [4 Important Notes](#4-important-notes)
    - [4.1 Package Manager](#41-package-manager)
  - [5 How to Use This Documentation](#5-how-to-use-this-documentation)
    - [5.1 For New Developers](#51-for-new-developers)
    - [5.2 Quick Start Option](#52-quick-start-option)
    - [5.3 For Specific Package Setup](#53-for-specific-package-setup)
    - [5.4 Quick Reference](#54-quick-reference)
    - [5.5 Troubleshooting](#55-troubleshooting)
  - [6 Environment Variables Quick Reference](#6-environment-variables-quick-reference)
  - [7 Package Update Strategy](#7-package-update-strategy)
    - [7.1 Stable Packages](#71-stable-packages)
    - [7.2 Development Packages](#72-development-packages)
    - [7.3 Update Verification](#73-update-verification)
    - [7.4 Version Constraints](#74-version-constraints)
  - [8 CI/CD Integration](#8-cicd-integration)
    - [8.1 GitHub Actions Workflows](#81-github-actions-workflows)
    - [8.2 CI/CD Setup](#82-cicd-setup)
    - [8.3 Required Secrets](#83-required-secrets)
    - [8.4 Local Testing](#84-local-testing)
  - [9 Documentation Standards](#9-documentation-standards)
  - [10 Contributing](#10-contributing)
  - [11 Custom Dashboard Experience](#11-custom-dashboard-experience)
    - [11.1 Goals and Audience](#111-goals-and-audience)
    - [11.2 Filament Asset Integration](#112-filament-asset-integration)
    - [11.3 Layout Architecture Updates](#113-layout-architecture-updates)
    - [11.4 Widget Grid Composition](#114-widget-grid-composition)
    - [11.5 Sidebar and Theme Controls](#115-sidebar-and-theme-controls)
    - [11.6 Testing and Verification](#116-testing-and-verification)
  - [12 Navigation](#12-navigation)

</details>

---

## 1 Introduction

This directory contains comprehensive setup and configuration documentation for the Laravel Livewire Starter Kit. All documentation is written in GitHub-flavored Markdown and provides detailed, step-by-step guidance suitable for developers of all experience levels.

### 1.1 Documentation Standards

- All section headings (`##` and deeper) must include sequential numeric prefixes (for example, `## 1 Section Title`, `### 1.1 Subsection Title`, `#### 1.1.1 Detail`).
- Each document ends with a navigation footer using the format `[← Previous Title](previous.md) | [↑ Top](#primary-heading-slug) | [Next Title →](next.md)`.
- The `↑ Top` link must point to the slug generated from the document’s top-level `#` heading, and the previous/next links wrap around the documentation sequence (connecting `README.md` and `800-notes-and-queries.md`).

### 1.2 Official Documentation Sources

This documentation references and builds upon official sources:

- **Laravel**: [Laravel 12.x Documentation](https://laravel.com/docs/12.x)
- **Livewire**: [Livewire Documentation](https://livewire.laravel.com/docs)
- **Pest**: [Pest Testing Framework Documentation](https://pestphp.com/docs)
- **Bun**: [Bun Documentation](https://bun.sh/docs)
- **Tailwind CSS**: [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- **Vite**: [Vite Documentation](https://vitejs.dev/guide)
- **Filament**: [Filament Documentation](https://filamentphp.com/docs)
- **Laravel Starter Kits**: [Laravel Starter Kits Documentation](https://laravel.com/docs/starter-kits)

For package-specific documentation, see the individual package documentation files.

---

## 2 Setup Order and Workflow

### 2.1 Recommended Setup Order

For new installations, follow this order:

1. **PHP Runtime** ([php-runtime.md](030-php-runtime.md))
    1. Install PHP 8.4+ (8.4.12 recommended)
    2. Verify required extensions
    3. Configure PHP settings
2. **Laravel Core** ([laravel-core.md](040-laravel-core.md))
    1. Verify Laravel installation
    2. Configure environment file
    3. Generate application key
    4. Set up database (SQLite recommended for development)
    5. Enable API routes and broadcasting
3. **Database Setup** ([laravel-core.md](040-laravel-core.md#database-configuration))
    1. Configure SQLite for development
    2. Enable WAL mode (recommended)
    3. Run migrations
4. **Frontend Build** ([frontend-build.md](130-frontend-build.md))
    1. Install Bun
    2. Install frontend dependencies (`bun install`)
    3. Build assets (`bun run build`)
5. **Livewire Ecosystem** ([livewire-ecosystem.md](050-livewire-ecosystem.md))
    1. Configure Livewire
    2. Set up Volt
    3. Configure Flux Pro (if using)
6. **Packages** - Install and configure remaining packages as needed:
    1. [Filament Admin Panel](060-admin-panel.md)
    2. [Authentication & Security](070-auth-security.md)
    3. [Queue & Monitoring](080-queue-monitoring.md)
    4. [Observability Tools](090-observability.md)
    5. [Spatie Packages](100-spatie-packages.md)
    6. [Search & Analytics](110-search-analytics.md)
7. **Development Tools** ([development-tools.md](120-development-tools.md))
    1. Set up debugging tools
    2. Configure code quality tools
    3. Set up testing framework (Pest 4)
    4. Install Playwright browsers

### 2.2 Quick Setup Commands

For experienced developers, here’s a condensed setup:

``` bash
# Install PHP dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Set up database (SQLite)
touch database/database.sqlite
php artisan migrate

# Install frontend dependencies
bun install
bun run build

# Install Playwright browsers (for testing)
bunx playwright install --with-deps
```

### 2.3 Verification Checklist

After setup, verify:

- [] PHP 8.4+ installed and configured
- [] Laravel application key generated
- [] Database connection working
- [] Frontend assets building
- [] All migrations run successfully
- [] Tests passing (`php artisan test`)

---

## 3 Documentation Structure

The documentation is organized into the following sections:

- [quick-start.md](010-quick-start.md) - Quick start guide for experienced developers
- [overview.md](020-overview.md) - Project overview, architecture, and package ecosystem
- [php-runtime.md](030-php-runtime.md) - PHP 8.4+ runtime requirements and setup
- [laravel-core.md](040-laravel-core.md) - Laravel Framework 12.x core setup
- [livewire-ecosystem.md](050-livewire-ecosystem.md) - Livewire 4, Volt, Flux, and Flux Pro
- [admin-panel.md](060-admin-panel.md) - Filament 5.x-dev admin panel setup
- [auth-security.md](070-auth-security.md) - Fortify, WorkOS, and authentication setup
- [queue-monitoring.md](080-queue-monitoring.md) - Horizon, Octane, and queue management
- [observability.md](090-observability.md) - Telescope, Activity Log, Health checks
- [spatie-packages.md](100-spatie-packages.md) - All Spatie Laravel packages configuration
- [search-analytics.md](110-search-analytics.md) - Scout, Typesense, Analytics setup
- [development-tools.md](120-development-tools.md) - Dev dependencies (Debugbar, IDE Helper, Rector, Pest, etc.)
- [spec-driven-development.md](125-spec-driven-development.md) - Spec-Kit workflow and specification-driven development
- [frontend-build.md](130-frontend-build.md) - Bun, Vite, Tailwind CSS 4, and frontend tooling
- [package-installation.md](135-package-installation.md) - Comprehensive Composer and JavaScript package installation guide
- [outstanding-questions.md](140-outstanding-questions.md) - Outstanding questions, decisions, and inconsistencies
- [patches.md](145-patches.md) - Composer patch system for package compatibility
- [troubleshooting.md](150-troubleshooting.md) - Centralized troubleshooting index

---

## 4 Important Notes

### 4.1 Package Manager

This project uses **Bun exclusively** for JavaScript/TypeScript package management.

IMPORTANT:
\* Always use `bun` instead of `npm`
\* Always use `bunx` instead of `npx`
\* Never use `yarn`
\* See [frontend-build.md](130-frontend-build.md#bun-package-manager) for details

---

## 5 How to Use This Documentation

### 5.1 For New Developers

Start with `020-overview.md` to understand the project structure and architecture, then proceed through the numbered sections in order.

### 5.2 Quick Start Option

For experienced developers, you can use [quick-start.md](010-quick-start.md) for a condensed setup guide.

### 5.3 For Specific Package Setup

Each document covers a specific set of packages. Use the table of contents in each document to find the package you need to configure.

### 5.4 Quick Reference

Configuration steps are provided in tabular format for easy scanning. Each package includes:

- Package overview and purpose
- Installation verification steps
- Configuration steps table
- Example configurations
- Integration points with other packages

### 5.5 Troubleshooting

If you encounter issues during setup, see [troubleshooting.md](150-troubleshooting.md) for a centralized troubleshooting guide with links to specific solutions.

---

## 6 Environment Variables Quick Reference

This is a quick reference for all environment variables used across the project. For detailed documentation, see the specific package documentation.

| Variable | Purpose | Document | Default |
|----|----|----|----|
| APP_NAME | Application name | [laravel-core.md](040-laravel-core.md) | Laravel |
| APP_ENV | Application environment | [laravel-core.md](040-laravel-core.md) | local |
| APP_KEY | Application encryption key | [laravel-core.md](040-laravel-core.md) | (generated) |
| APP_URL | Application URL | [laravel-core.md](040-laravel-core.md) | <http://localhost> |
| DB_CONNECTION | Database driver (sqlite/mysql/pgsql) | [laravel-core.md](040-laravel-core.md) | sqlite |
| DB_DATABASE | Database path or name | [laravel-core.md](040-laravel-core.md) | database/database.sqlite |
| BROADCAST_DRIVER | Broadcasting driver | [laravel-core.md](040-laravel-core.md) | log |
| QUEUE_CONNECTION | Queue driver | [queue-monitoring.md](080-queue-monitoring.md) | sync |
| REDIS_HOST | Redis server host | [queue-monitoring.md](080-queue-monitoring.md) | 127.0.0.1 |
| TYPESENSE_HOST | Typesense server host | [search-analytics.md](110-search-analytics.md) | localhost |
| TYPESENSE_API_KEY | Typesense API key | [search-analytics.md](110-search-analytics.md) | (required) |
| ANALYTICS_PROPERTY_ID | Google Analytics property ID | [spatie-packages.md](100-spatie-packages.md) | (empty) |
| FLUX_PRO_USERNAME | Flux Pro username | [livewire-ecosystem.md](050-livewire-ecosystem.md) | (required) |
| FLUX_PRO_PASSWORD | Flux Pro password | [livewire-ecosystem.md](050-livewire-ecosystem.md) | (required) |

> [!NOTE]
> This is a quick reference. For complete configuration details, see the specific package documentation.

---

## 7 Package Update Strategy

### 7.1 Stable Packages

For packages with stable versions (e.g., `^12.36`, `^4.0`):

``` bash
# Update all stable packages
composer update

# Update specific package
composer update laravel/framework

# Update frontend dependencies
bun update
```

**Best Practices:**
\* Review changelogs before updating
\* Test after updates
\* Update packages regularly for security patches

### 7.2 Development Packages

For packages using development versions (e.g., `^5.x-dev`, `dev-master`):

``` bash
# Update development packages (careful!)
composer update filament/filament laravel/folio livewire/volt

# Update to latest commit
composer update laravel/folio --prefer-source
```

**WARNING**: Development packages may have breaking changes. Always:

- Check the package’s changelog or commit history
- Test thoroughly after updates
- Consider pinning to specific commits if stability is critical
- Update one package at a time to isolate issues

### 7.3 Update Verification

After updating packages:

``` bash
# Run tests
php artisan test

# Check for deprecated code
composer analyse

# Format code
composer cs-fix

# Check frontend builds
bun run build
```

### 7.4 Version Constraints

The project uses different version constraints:

- **Stable**: `^12.36` - Allows minor updates (12.36, 12.37, etc.)
- **Development**: `^5.x-dev` - Allows any development version
- **Specific**: `dev-master` - Uses latest master branch

See [overview.md](020-overview.md#version-constraints) for details.

---

## 8 CI/CD Integration

### 8.1 GitHub Actions Workflows

The project includes GitHub Actions workflows in `.github/workflows/`:

- **tests.yml** - Runs PHP tests, static analysis, and browser tests
- **browser-tests.yml** - Dedicated browser testing workflow

### 8.2 CI/CD Setup

The workflows automatically:

- Set up PHP 8.4
- Set up Bun and Node.js
- Install dependencies (Composer and Bun)
- Configure Flux Pro authentication from secrets
- Run tests and static analysis
- Build frontend assets

### 8.3 Required Secrets

For CI/CD to work, configure these GitHub secrets:

- `FLUX_USERNAME` - Flux Pro username
- `FLUX_LICENSE_KEY` - Flux Pro license key

### 8.4 Local Testing

Test CI/CD workflows locally using [act](https://act.sh) or by running the commands manually:

``` bash
# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader
bun install

# Run tests
php artisan test

# Run static analysis
composer analyse

# Build assets
bun run build
```

---

## 9 Documentation Standards

All documentation follows these principles:

- **Clarity for Junior Developers**: All content is written to be clear and actionable for developers at any experience level
- **Complete Coverage**: Every package in `composer.json` and `package.json` is documented
- **Actionable Steps**: All configuration steps are testable and verifiable
- **Cross-References**: Related packages are cross-referenced for easy navigation

---

## 10 Contributing

When updating this documentation:

- Ensure all code examples are tested and working
- Update cross-references when adding new packages
- Maintain consistent formatting and structure
- Verify all configuration steps are still accurate

---

## 11 Custom Dashboard Experience

### 11.1 Goals and Audience

The starter kit ships with an opinionated dashboard tailored for junior developers who need clear cues about their next steps. The dashboard demonstrates how to blend Filament widgets with bespoke cards while preserving the Livewire Starter Kit design language. Review the implementation before creating new panels so you can follow the same conventions.

### 11.2 Filament Asset Integration

Filament styles and scripts are now loaded globally so every page can opt into Filament resources without repeating boilerplate.

- `resources/views/partials/head.blade.php` adds `@filamentStyles` immediately after `@livewireStyles`, ensuring Filament CSS is available inside the `<head>` element.
- `resources/views/components/layouts/app/sidebar.blade.php` appends `@filamentScripts` alongside the existing Livewire and Flux script directives. These scripts load once at the end of the layout so any nested child view can render Filament widgets without extra includes.
- `app/Providers/Filament/SupportCustomizationServiceProvider.php` merges `config/filament/assets.php`, then hooks the `Filament::serving` event. This provider:
  - Sets script attributes (for example `defer`, `data-turbo-eval="false"`) to match our Turbo-friendly defaults.
  - Allows you to opt out of specific scripts or Alpine components via configuration.
  - Is registered in `bootstrap/providers.php`, so the behaviour is available in every environment.
- `tests/Feature/DashboardTest.php` contains regression coverage confirming script attributes respect the configuration. Run `./vendor/bin/pest tests/Feature/DashboardTest.php --filter="filament script attributes"` to re-check after making changes.

> [!TIP]
> If you introduce new Filament panels, prefer updating `config/filament/assets.php` instead of hard-coding attributes in Blade. This keeps the asset contract in one place.

### 11.3 Layout Architecture Updates

The primary application layout lives in `resources/views/components/layouts/app/sidebar.blade.php`. Key updates:

1. **Collapsible Sidebar**
   The `<flux:sidebar>` component now sets `collapsible="desktop"`, enabling a compact icon mode. A desktop toggle button (`<flux:sidebar.toggle icon="arrows-right-left" />`) sits beside the mobile close button so users can collapse or expand the sidebar on larger screens.
2. **Script Stack**
   The layout ends with:
   ```blade
   @livewireScripts
   @filamentScripts
   @fluxScripts
   ```
   Keep any additional global scripts here so they execute after the content area renders.
3. **Tooltips and Accessibility**
   Each navigation item includes a `data-tooltip` attribute, ensuring labels remain discoverable when the sidebar is collapsed to icons.

### 11.4 Widget Grid Composition

The dashboard view at `resources/views/dashboard.blade.php` was rewritten to replace placeholder graphics with production-ready cards:

- **Top Row (Three Cards)**
  1. **Welcome Card** – Displays the signed-in user’s name and email, plus quick actions to manage their profile or view activity. Values come from `auth()->user()`, so wire new data through the authentication layer rather than embedding dummy text.
  2. **Quick Links** – Provides starter kit documentation shortcuts. Buttons use Tailwind + Flux utility classes matching the design system.
  3. **Onboarding Checklist** – Bullet list using semantic `<ul>` markup so screen readers announce progress clearly.

- **Second Row (Three Cards)**
  1. **Filament Toolkit** – Links to Filament docs and GitHub. Demonstrates how to overlay `x-placeholder-pattern` for subtle backgrounds.
  2. **Starter Kit Highlights** – Curated resource list; each `<a>` includes `target="_blank"` and `rel="noopener noreferrer"` for safety.
  3. **Helpful Tips** – Shows inline code snippets using `<code>` with dark-mode aware styling.

Every card reuses a consistent wrapper: `rounded-xl`, `border`, `bg-white` / `dark:bg-zinc-900`, and padding (`p-6`). When building new cards, copy this shell so spacing remains consistent.

At the bottom, the Filament `filament-info-widget` renders inside a scrollable container. Wrap additional widgets in similar containers to maintain consistent padding and scroll behaviour.

### 11.5 Sidebar and Theme Controls

Two additional UX enhancements were introduced:

1. **Account Menu Theme Toggle**
   Both desktop and mobile menus use:
   ```blade
   <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
       <flux:radio value="light" icon="sun" />
       <flux:radio value="dark" icon="moon" />
       <flux:radio value="system" icon="computer-desktop" />
   </flux:radio.group>
   ```
   Flux automatically persists the choice through `localStorage` and applies the theme by calling `window.Flux.applyAppearance`.

2. **Updated Navigation Icons**
   Heroicons were corrected to valid identifiers (`arrows-right-left`, `chevron-up-down`). Reference [heroicons.com](https://heroicons.com/) when adding new icons to avoid runtime warnings.

### 11.6 Testing and Verification

Follow this checklist after editing the dashboard or layout:

1. **Asset Coverage** – Run `./vendor/bin/pest tests/Feature/DashboardTest.php`. Address any failures before committing.
2. **Manual Browser Pass** – Verify the sidebar collapse button, theme toggle, and Filament widget render correctly in both light and dark modes.
3. **Lighthouse Quick Check** – Use Chrome’s Lighthouse to ensure accessibility stays above 90. Pay attention to colour contrast if you adjust Tailwind classes.
4. **Responsive Layout** – Test at 1280px, 768px, and 375px widths. Cards should wrap cleanly (`md:grid-cols-3` handles the breakpoint).

Keep this section up to date whenever you introduce new widgets, alter the grid, or change global asset handling.

---

## 12 Navigation

[← Setup Notes and Queries](800-notes-and-queries.md) | [↑ Top](#setup-documentation) | [Quick Start Guide →](010-quick-start.md)

[← Setup Notes and Queries](800-notes-and-queries.md) | [↑ Top](#setup-documentation) | [Quick Start Guide →](010-quick-start.md)

---
