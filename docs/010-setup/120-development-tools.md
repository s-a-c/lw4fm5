# Development Dependencies Setup

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

<details>
<summary>Expand for Table of Contents</summary>

- [Development Dependencies Setup](#development-dependencies-setup)
  - [1 Introduction](#1-introduction)
  - [2 Debugging Tools](#2-debugging-tools)
    - [2.1 Laravel Debugbar](#21-laravel-debugbar)
    - [2.2 Laravel IDE Helper](#22-laravel-ide-helper)
    - [2.3 Ray](#23-ray)
    - [2.4 Web Tinker](#24-web-tinker)
  - [3 Code Quality Tools](#3-code-quality-tools)
    - [3.1 Larastan (PHPStan)](#31-larastan-phpstan)
    - [3.2 Laravel Pint](#32-laravel-pint)
    - [3.3 Rector](#33-rector)
    - [3.4 Type Perfect](#34-type-perfect)
  - [4 Testing Framework](#4-testing-framework)
    - [4.1 Pest 4](#41-pest-4)
    - [4.2 Pest Plugins](#42-pest-plugins)
    - [4.3 PHPUnit](#43-phpunit)
    - [4.4 Mockery](#44-mockery)
    - [4.5 Laravel Test Assertions](#45-laravel-test-assertions)
    - [4.6 Browser Testing (Playwright)](#46-browser-testing-playwright)
      - [4.6.1 Installation](#461-installation)
      - [4.6.2 Browser Installation](#462-browser-installation)
      - [4.6.3 Configuration](#463-configuration)
      - [4.6.4 Running Browser Tests](#464-running-browser-tests)
      - [4.6.5 System Dependencies](#465-system-dependencies)
      - [4.6.6 Troubleshooting](#466-troubleshooting)
      - [4.6.7 Integration with Pest 4](#467-integration-with-pest-4)
  - [5 Development Utilities](#5-development-utilities)
    - [5.1 Laravel Boost](#51-laravel-boost)
    - [5.2 Laravel Pail](#52-laravel-pail)
    - [5.3 Laravel Sail](#53-laravel-sail)
    - [5.4 Blueprint](#54-blueprint)
    - [5.5 Solo](#55-solo)
  - [6 Spatie Dev Tools](#6-spatie-dev-tools)
    - [6.1 Blade Comments](#61-blade-comments)
    - [6.2 Horizon Watcher](#62-horizon-watcher)
    - [6.3 Login Link](#63-login-link)
    - [6.4 Missing Page Redirector](#64-missing-page-redirector)
    - [6.5 Queueable Action](#65-queueable-action)
    - [6.6 Pest Snapshots](#66-pest-snapshots)
  - [7 Version Control and Git Workflow](#7-version-control-and-git-workflow)
    - [7.1 Git Flow Setup](#71-git-flow-setup)
      - [7.1.1 Initialization](#711-initialization)
      - [7.1.2 Common Git Flow Commands](#712-common-git-flow-commands)
      - [7.1.3 Workflow Overview](#713-workflow-overview)
    - [7.2 Jujutsu (jj) Integration](#72-jujutsu-jj-integration)
      - [7.2.1 Installation](#721-installation)
      - [7.2.2 Initializing Jujutsu in a Git Repository](#722-initializing-jujutsu-in-a-git-repository)
      - [7.2.3 Working with Git Flow Using Jujutsu](#723-working-with-git-flow-using-jujutsu)
      - [7.2.4 Jujutsu Best Practices with Git Flow](#724-jujutsu-best-practices-with-git-flow)
      - [7.2.5 Common Jujutsu Commands](#725-common-jujutsu-commands)
      - [7.2.6 Troubleshooting](#726-troubleshooting)
    - [7.3 Recommended Workflow](#73-recommended-workflow)
    - [7.4 Spec-Kit Integration](#74-spec-kit-integration)
      - [7.4.1 Installation](#741-installation)
      - [7.4.2 Overview](#742-overview)
      - [7.4.3 Integration with Git Flow and Jujutsu](#743-integration-with-git-flow-and-jujutsu)
      - [7.4.4 Best Practices](#744-best-practices)
  - [8 Other Dev Tools](#8-other-dev-tools)
    - [8.1 Composer Normalize](#81-composer-normalize)
    - [8.2 Security Advisories](#82-security-advisories)
    - [8.3 Faker](#83-faker)
  - [9 Next Steps](#9-next-steps)
  - [10 Navigation](#10-navigation)

</details>

---

## 1 Introduction

This document covers all development dependencies including debugging tools, code quality tools, testing frameworks, and development utilities.

---

## 2 Debugging Tools

### 2.1 Laravel Debugbar

**Package**: `barryvdh/laravel-debugbar` ^3.13
**Purpose**: Debug toolbar for Laravel applications
**Verification**: `composer show barryvdh/laravel-debugbar`

### 2.2 Laravel IDE Helper

**Package**: `barryvdh/laravel-ide-helper` ^3.6
**Purpose**: IDE autocomplete and helper files
**Commands**:

``` bash
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

### 2.3 Ray

**Package**: `spatie/laravel-ray` ^1.41
**Purpose**: Debugging tool for Laravel
**Usage**: `ray($variable)` in code

### 2.4 Web Tinker

**Package**: `spatie/laravel-web-tinker` ^1.10
**Purpose**: Web-based REPL for Laravel
**Access**: Available at `/tinker` route

## 3 Code Quality Tools

### 3.1 Larastan (PHPStan)

**Package**: `larastan/larastan` ^3.8
**Purpose**: Static analysis for Laravel
**Command**: `composer analyse` or `vendor/bin/phpstan analyse`

### 3.2 Laravel Pint

**Package**: `laravel/pint` ^1.25
**Purpose**: Code formatter
**Command**: `vendor/bin/pint` or `composer cs-fix`

### 3.3 Rector

**Package**: `rector/rector` ^2.2
**Purpose**: Automated code refactoring
**Command**: `vendor/bin/rector`

### 3.4 Type Perfect

**Package**: `rector/type-perfect` ^2.1
**Purpose**: Additional type checking for Rector
**Usage**: Works with Rector automatically

## 4 Testing Framework

### 4.1 Pest 4

**Package**: `pestphp/pest` ^4.1
**Purpose**: Modern testing framework
**Key Features**:
\* Browser testing
\* Type coverage
\* Visual regression testing
\* Test sharding

**Commands**:

``` bash
php artisan test
pest --filter test_name
pest --type-coverage
```

> [!IMPORTANT]
> This project uses **Pest 4 exclusively** for all tests. PHPUnit is installed as a dependency of Pest but should not be used directly. All tests must be written in Pest syntax. Any existing PHPUnit tests should be migrated to Pest.

**Source**: [Pest Documentation](https://pestphp.com/docs) \| [Pest Browser Testing](https://pestphp.com/docs/browser-testing)

### 4.2 Pest Plugins

- `pestphp/pest-plugin-arch` ^4.0 - Architecture testing
- `pestphp/pest-plugin-browser` ^4.1 - Browser testing
- `pestphp/pest-plugin-faker` ^4.0 - Faker integration
- `pestphp/pest-plugin-laravel` ^4.0 - Laravel integration
- `pestphp/pest-plugin-profanity` ^4.2 - Profanity detection
- `pestphp/pest-plugin-type-coverage` ^4.0 - Type coverage

### 4.3 PHPUnit

**Package**: `phpunit/phpunit` ^12.4
**Purpose**: PHP testing framework (dependency of Pest 4, not used directly)

> [!NOTE]
> PHPUnit is installed as a dependency of Pest 4 but should not be used directly. All tests must be written using Pest syntax.

### 4.4 Mockery

**Package**: `mockery/mockery` ^1.6
**Purpose**: Mocking framework for tests

### 4.5 Laravel Test Assertions

**Package**: `jasonmccreary/laravel-test-assertions` ^2.8
**Purpose**: Additional test assertions for Laravel

### 4.6 Browser Testing (Playwright)

**Package**: `playwright` ^1.56.1 (frontend dependency)
**Purpose**: End-to-end browser testing framework
**Architectural Role**: Provides real browser testing capabilities for Pest 4

#### 4.6.1 Installation

Playwright is installed via Bun:

``` bash
# Install Playwright browsers
bunx playwright install --with-deps

# Or use the npm script
bun run playwright:install
```

The `--with-deps` flag installs system dependencies required for browsers.

#### 4.6.2 Browser Installation

Playwright supports multiple browsers:

- **Chromium** - Google Chrome/Edge (default)
- **Firefox** - Mozilla Firefox
- **WebKit** - Safari engine

All browsers are installed by default. To install specific browsers:

``` bash
# Install only Chromium
bunx playwright install chromium

# Install all browsers
bunx playwright install --with-deps
```

#### 4.6.3 Configuration

Playwright configuration is typically in `playwright.config.ts` or integrated with Pest 4. For Pest 4 browser testing:

``` php
// tests/Browser/ExampleTest.php
use Pest\Laravel\Laravel;

it('can visit homepage', function () {
    $this->browse(function ($browser) {
        $browser->visit('/')
                ->assertSee('Welcome');
    });
});
```

#### 4.6.4 Running Browser Tests

``` bash
# Run all browser tests
bun run test:browser

# Run specific test file
bunx playwright test tests/Browser/ExampleTest.php

# Run with UI mode (interactive)
bunx playwright test --ui

# Run in headed mode (see browser)
bunx playwright test --headed

# Run specific browser
bunx playwright test --project=chromium
```

**Source**: [Playwright Documentation](https://playwright.dev/docs/intro) \| [Playwright Running Tests](https://playwright.dev/docs/running-tests)

#### 4.6.5 System Dependencies

Playwright requires system dependencies. On Ubuntu/Debian:

``` bash
# Install system dependencies
bunx playwright install-deps
```

On macOS, dependencies are usually installed automatically. On Windows, no additional setup is needed.

#### 4.6.6 Troubleshooting

**Issue**: Browsers not found
**Solution**: Run `bunx playwright install --with-deps`

**Issue**: Tests fail with headless errors
**Solution**: Run with `--headed` flag to debug, or check system dependencies

**Issue**: Slow test execution
**Solution**: Use `--workers=1` to run tests sequentially, or optimize test setup

#### 4.6.7 Integration with Pest 4

Pest 4’s browser testing plugin integrates Playwright:

``` php
use function Pest\Laravel\visit;

it('can visit homepage', function () {
    $page = visit('/');

    $page->assertSee('Welcome')
         ->assertNoJavascriptErrors()
         ->click('Sign In')
         ->assertSee('Email');
});
```

See [Testing Framework: Pest 4](#41-pest-4) section for more details on browser testing patterns.

## 5 Development Utilities

### 5.1 Laravel Boost

**Package**: `laravel/boost` ^1.6
**Purpose**: Development tools and MCP server

### 5.2 Laravel Pail

**Package**: `laravel/pail` ^1.2
**Purpose**: Real-time log viewer
**Command**: `php artisan pail`

### 5.3 Laravel Sail

**Package**: `laravel/sail` ^1.47
**Purpose**: Docker-based development environment

### 5.4 Blueprint

**Package**: `laravel-shift/blueprint` ^2.12
**Purpose**: Code generation from YAML definitions

### 5.5 Solo

**Package**: `soloterm/solo` ^0.5.0
**Purpose**: Terminal utilities

## 6 Spatie Dev Tools

### 6.1 Blade Comments

**Package**: `spatie/laravel-blade-comments` ^2.0
**Purpose**: HTML comments in Blade templates

### 6.2 Horizon Watcher

**Package**: `spatie/laravel-horizon-watcher` ^1.1
**Purpose**: Monitor Horizon status

### 6.3 Login Link

**Package**: `spatie/laravel-login-link` ^1.6
**Purpose**: Secure login links for development

### 6.4 Missing Page Redirector

**Package**: `spatie/laravel-missing-page-redirector` ^2.11
**Purpose**: Redirect 404s to similar pages

### 6.5 Queueable Action

**Package**: `spatie/laravel-queueable-action` ^2.16
**Purpose**: Queue actions as jobs

### 6.6 Pest Snapshots

**Package**: `spatie/pest-plugin-snapshots` ^2.2
**Purpose**: Snapshot testing for Pest

## 7 Version Control and Git Workflow

### 7.1 Git Flow Setup

This project uses **Git Flow** for branch management, providing a structured workflow for feature development, releases, and hotfixes.

#### 7.1.1 Initialization

Git Flow has been initialized with the following configuration:

- **Main branch**: `main` (production-ready code)
- **Development branch**: `develop` (integration branch for features)
- **Feature branches**: `feature/` (new features)
- **Release branches**: `release/` (preparing releases)
- **Hotfix branches**: `hotfix/` (urgent production fixes)
- **Support branches**: `support/` (maintaining older versions)
- **Version tag prefix**: (none)

#### 7.1.2 Common Git Flow Commands

```bash
# Start a new feature
git flow feature start feature-name

# Finish a feature (merges to develop)
git flow feature finish feature-name

# Start a release
git flow release start 1.0.0

# Finish a release (merges to main and develop, creates tag)
git flow release finish 1.0.0

# Start a hotfix
git flow hotfix start 1.0.1

# Finish a hotfix (merges to main and develop, creates tag)
git flow hotfix finish 1.0.1
```

#### 7.1.3 Workflow Overview

1. **Feature Development**
   - Create feature branch from `develop`
   - Develop and test feature
   - Merge back to `develop` when complete

2. **Release Preparation**
   - Create release branch from `develop`
   - Final testing and bug fixes
   - Merge to both `main` and `develop`, create version tag

3. **Hotfixes**
   - Create hotfix branch from `main`
   - Fix critical issues
   - Merge to both `main` and `develop`, create version tag

### 7.2 Jujutsu (jj) Integration

This project supports using **Jujutsu (jj)** as an alternative version control interface while maintaining Git Flow compatibility. Jujutsu provides a modern, conflict-free workflow that can work alongside Git.

#### 7.2.1 Installation

```bash
# macOS (Homebrew)
brew install jujutsu

# Verify installation
jj --version
```

#### 7.2.2 Initializing Jujutsu in a Git Repository

If the repository is already a Git repository, initialize Jujutsu:

```bash
# Initialize jj in existing Git repo
jj git init

# This creates a .jj/ directory and sets up jj to work with Git
```

#### 7.2.3 Working with Git Flow Using Jujutsu

Jujutsu can work alongside Git Flow by using Git branches as synchronization points:

##### 7.2.3.1 Starting a Feature

```bash
# Using jj (recommended for day-to-day work)
jj new -m "Start feature: feature-name"
jj branch create feature/feature-name

# Or using git flow (creates Git branch)
git flow feature start feature-name
jj git fetch  # Sync jj with Git branches
jj co feature/feature-name  # Checkout the Git branch in jj
```

##### 7.2.3.2 Daily Development Workflow

```bash
# Create a new change (like a commit)
jj new -m "Implement user authentication"

# Continue working on the same change
jj new  # Creates a new change on top

# View your changes
jj log

# Edit a change's description
jj describe -m "Updated description"

# Abandon a change
jj abandon <change-id>
```

##### 7.2.3.3 Syncing with Git Branches

```bash
# Sync jj changes to Git branch
jj git push

# Pull latest from Git
jj git fetch
jj rebase -d @git/main  # Rebase on main
jj rebase -d @git/develop  # Rebase on develop
```

##### 7.2.3.4 Finishing a Feature

```bash
# Squash or merge changes as needed
jj squash -r <change-id> -r <another-change-id>

# Sync to Git branch
jj git push

# Use git flow to finish (merges to develop)
git flow feature finish feature-name

# Or manually merge in jj
jj new -m "Merge feature/feature-name"
jj merge @git/feature/feature-name @git/develop
jj git push
```

#### 7.2.4 Jujutsu Best Practices with Git Flow

1. **Use Git branches as synchronization points**
   - Create Git branches for features, releases, and hotfixes
   - Use `jj git fetch` regularly to stay in sync
   - Push jj changes to Git branches before finishing features

2. **Keep changes focused**
   - Create a new jj change for each logical unit of work
   - Use `jj describe` to update change messages
   - Squash related changes before merging to Git branches

3. **Leverage jj's conflict-free model**
   - Use `jj rebase` freely to reorganize changes
   - Merge branches without worrying about conflicts
   - Use `jj split` to break large changes into smaller ones

4. **Maintain Git Flow structure**
   - Always finish features using `git flow feature finish`
   - Use Git tags for releases (jj will sync them)
   - Keep `main` and `develop` branches in sync

#### 7.2.5 Common Jujutsu Commands

```bash
# View status
jj status

# View log
jj log

# Create new change
jj new -m "Description"

# Edit current change
jj edit <change-id>

# Split a change
jj split

# Squash changes
jj squash -r <change-id>

# Rebase on another change
jj rebase -d <target-change-id>

# Merge branches
jj merge <source> <destination>

# Sync with Git
jj git fetch
jj git push

# Abandon a change
jj abandon <change-id>
```

#### 7.2.6 Troubleshooting

**Issue**: jj changes not appearing in Git
**Solution**: Run `jj git push` to sync changes to Git branches

**Issue**: Git branches not visible in jj
**Solution**: Run `jj git fetch` to update jj's view of Git branches

**Issue**: Conflicts when syncing
**Solution**: jj handles most conflicts automatically. Use `jj resolve` if needed

**Issue**: Need to switch between jj and Git
**Solution**: Both can work simultaneously. Use `jj git` commands to sync, or use Git commands directly when needed

### 7.3 Recommended Workflow

For optimal workflow combining Git Flow and Jujutsu:

1. **Start features**: Use `git flow feature start` to create Git branch structure
2. **Daily development**: Use `jj new` for creating changes, `jj log` for viewing history
3. **Sync regularly**: Run `jj git push` before finishing features
4. **Finish features**: Use `git flow feature finish` to maintain Git Flow structure
5. **Releases**: Use `git flow release` commands (jj will sync automatically)

This approach gives you jj's modern workflow for day-to-day development while maintaining Git Flow's structured release process.

### 7.4 Spec-Kit Integration

This project uses **Spec-Kit** for Specification-Driven Development (SDD), enabling specification-first development workflows with AI-powered planning and task generation.

#### 7.4.1 Installation

Spec-Kit is installed via `uvx`:

```bash
# Install spec-kit globally (one-time setup)
uvx --from git+https://github.com/github/spec-kit.git specify init

# Verify installation
specify --version
```

#### 7.4.2 Overview

Spec-Kit provides AI-powered commands (`/speckit.plan`, `/speckit.tasks`, `/speckit.clarify`, `/speckit.checklist`) that are used as prompts within AI coding assistants (Cursor, Claude Desktop, etc.). These commands generate specification artifacts from specifications written in markdown.

**Key Commands**:

- `/speckit.plan` - Generates technical implementation plan from specification
- `/speckit.tasks` - Generates actionable tasks from plan
- `/speckit.clarify` - Generates clarifications for ambiguous requirements
- `/speckit.checklist` - Generates quality checklists (architecture, security, performance, etc.)

#### 7.4.3 Integration with Git Flow and Jujutsu

Spec-Kit workflows use **separate specification branches** from code branches:

1. **Spec Branches**: `spec/###-feature-name` (e.g., `spec/001-base-platform`)
2. **Feature Branches**: `feature/###-feature-name` (e.g., `feature/001-base-platform`)

**Workflow**:

1. Create spec branch: `spec/###-feature-name`
2. Write specification: `specs/###-feature-name/spec.md`
3. Generate plan using `/speckit.plan` in AI agent
4. Generate tasks using `/speckit.tasks` in AI agent
5. Review and finalize spec artifacts
6. Merge spec branch to `develop`
7. Create feature branch from spec for implementation

**Helper Scripts**: See `scripts/spec/README.md` for automation and helper scripts documentation.

#### 7.4.4 Best Practices

1. **Keep specs separate from code**: Specifications live in `spec/` branches, code in `feature/` branches
2. **Commit all spec artifacts**: Plan, tasks, checklists, and contracts should all be committed
3. **Use numeric prefixes**: Match spec directory structure (e.g., `001-`, `002-`)
4. **Validate before finishing**: Ensure `spec.md`, `plan.md`, and `tasks.md` exist before merging
5. **Reference specs in features**: Link feature branches to their specification branches/commits

**Source**: [Spec-Driven Development Documentation →](125-spec-driven-development.md)

## 8 Other Dev Tools

### 8.1 Composer Normalize

**Package**: `ergebnis/composer-normalize` ^2.48
**Purpose**: Normalize composer.json format

### 8.2 Security Advisories

**Package**: `roave/security-advisories` dev-latest
**Purpose**: Prevent installation of insecure packages

### 8.3 Faker

**Package**: `fakerphp/faker` ^1.24
**Purpose**: Fake data generation for tests

## 9 Next Steps

[Frontend Build →](130-frontend-build.md)

---

## 10 Navigation

[← Scout, Typesense, and Analytics Setup](110-search-analytics.md) | [↑ Top](#development-dependencies-setup) | [Spec-Driven Development →](125-spec-driven-development.md)
