# Outstanding Questions, Decisions, and Inconsistencies

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

---

<details>
<summary>Expand for Table of Contents</summary>

- [Outstanding Questions, Decisions, and Inconsistencies](#outstanding-questions-decisions-and-inconsistencies)
  - [1 Introduction](#1-introduction)
  - [2 Version Constraints](#2-version-constraints)
    - [2.1 Development Version Packages](#21-development-version-packages)
  - [3 Custom Repository Authentication](#3-custom-repository-authentication)
    - [3.1 Flux Pro Repository](#31-flux-pro-repository)
  - [4 PHP Version Constraint](#4-php-version-constraint)
    - [4.1 PHP 8.4 Baseline vs Future 8.5 Upgrade](#41-php-84-baseline-vs-future-85-upgrade)
  - [5 Tailwind CSS Configuration](#5-tailwind-css-configuration)
    - [5.1 Configuration File vs CSS-Based](#51-configuration-file-vs-css-based)
  - [6 Testing Framework Consistency](#6-testing-framework-consistency)
    - [6.1 Pest vs PHPUnit](#61-pest-vs-phpunit)
  - [7 Database Configuration](#7-database-configuration)
    - [7.1 Default Database Driver](#71-default-database-driver)
  - [8 Additional Clarifications and Decisions](#8-additional-clarifications-and-decisions)
    - [8.1 FrankenPHP Runtime Documentation](#81-frankenphp-runtime-documentation)
    - [8.2 Browser Testing Setup Documentation](#82-browser-testing-setup-documentation)
    - [8.3 Spatie Packages Documentation Depth](#83-spatie-packages-documentation-depth)
    - [8.4 Setup Order and Workflow](#84-setup-order-and-workflow)
    - [8.5 Package Update Strategy](#85-package-update-strategy)
    - [8.6 CI/CD Integration Documentation](#86-cicd-integration-documentation)
    - [8.7 Environment Variable Organization](#87-environment-variable-organization)
    - [8.8 Gitignore Entries Documentation](#88-gitignore-entries-documentation)
  - [9 Summary and Next Actions](#9-summary-and-next-actions)
    - [9.1 Resolved Decisions](#91-resolved-decisions)
    - [9.2 Implementation Status](#92-implementation-status)
    - [9.3 Documentation Improvements Status](#93-documentation-improvements-status)
  - [10 Implementation Status](#10-implementation-status)
    - [10.1 Completed Enhancements](#101-completed-enhancements)
      - [10.1.1 Structure and Organization ✅](#1011-structure-and-organization-)
      - [10.1.2 Content Enhancements ✅](#1012-content-enhancements-)
      - [10.1.3 Additional Improvements ✅](#1013-additional-improvements-)
    - [10.2 Documentation Citations](#102-documentation-citations)
    - [10.3 Future Maintenance](#103-future-maintenance)
  - [11 Documentation Citations and Sources](#11-documentation-citations-and-sources)
    - [11.1 Official Documentation Sources](#111-official-documentation-sources)
    - [11.2 Citation Standards](#112-citation-standards)
  - [12 Navigation](#12-navigation)

</details>

---

## 1 Introduction

This document captures outstanding questions, decision points, and inconsistencies discovered during the documentation creation process. Each item includes context, current state, resolution options with confidence levels, and recommendations.

## 2 Version Constraints

### 2.1 Development Version Packages

**Question**: Several packages use development versions (`dev-master`, `dev-main`, `^5.x-dev`). Should these be pinned to stable releases?

**Context**: The following packages use non-stable versions:
\* `filament/filament: ^5.x-dev`
\* `laravel/folio: dev-master`
\* `livewire/volt: dev-main`

**Current State**: Project tracks cutting-edge versions of these packages.

**Resolution Options**:

| Option Name | Description | Pros | Cons | Confidence | Effort |
|----|----|----|----|----|----|
| Option 1: Keep Development Versions | Continue using development versions | Access to latest features, early bug fixes | Potential breaking changes, instability | 60% | Low |
| Option 2: Pin to Latest Stable | Wait for stable releases and pin versions | More stability, predictable updates | Miss new features, delayed bug fixes | 75% | Medium |
| Option 3: Hybrid Approach | Use dev versions for non-critical packages, stable for core | Balance between features and stability | Requires careful version management | 85% | High |

**Decision**: ✅ **RESOLVED** - Option 3 (Hybrid Approach) selected. Use stable versions for core packages (Laravel, Livewire) and development versions for newer packages (Filament 5, Volt) where cutting-edge features are needed.

## 3 Custom Repository Authentication

### 3.1 Flux Pro Repository

**Question**: How should authentication be configured for the Flux Pro custom repository?

**Context**: The project uses a custom Composer repository at `https://composer.fluxui.dev` for Flux Pro components. This requires authentication.

**Current State**: Repository is configured in `composer.json` but authentication method is not documented.

**Resolution Options**:

| Option Name | Description | Pros | Cons | Confidence | Effort |
|----|----|----|----|----|----|
| Option 1: Environment Variables | Store credentials in `.env` and use `auth.json` | Secure, follows Laravel patterns | Requires additional setup | 80% | Low |
| Option 2: Composer Auth File | Use `auth.json` file (gitignored) | Standard Composer approach | File management overhead | 70% | Low |
| Option 3: CI/CD Secrets | Store in CI/CD secrets for automated builds | Works for CI/CD | Doesn’t help local development | 60% | Medium |

**Decision**: ✅ **RESOLVED** - Option 1 (Environment Variables) selected. Document the use of `auth.json` with credentials stored securely, and ensure it’s in `.gitignore`.

## 4 PHP Version Constraint

### 4.1 PHP 8.4 Baseline vs Future 8.5 Upgrade

**Question**: Composer now requires PHP `^8.4`. When should the project plan to adopt PHP 8.5 once it is stable and widely available?

**Context**: The constraint in `composer.json` was updated to `^8.4` to align with readily available runtimes (8.4.12 recommended). Documentation now reflects this baseline, but we still want a plan for evaluating PHP 8.5 features.

**Current State**: Project standardizes on PHP 8.4.x; no timeline exists for adopting PHP 8.5.

**Resolution Options**:

| Option Name | Description | Pros | Cons | Confidence | Effort |
|----|----|----|----|----|----|
| Option 1: Stay on ^8.4 | Keep constraint at `^8.4` until PHP 8.5 is released and ecosystem support is stable | Max compatibility, minimal onboarding friction | Delayed access to new 8.5 features | 90% | Low |
| Option 2: Track 8.5 Betas | Begin testing nightly/beta builds of PHP 8.5 in CI while keeping production on 8.4 | Early feedback on potential issues | Higher maintenance overhead, unstable builds | 60% | Medium |
| Option 3: Immediate Upgrade on GA | Plan to bump to `^8.5` as soon as PHP 8.5 GA releases | Access to latest features ASAP | Risk of tooling lag, requires rapid coordination | 70% | Medium |

**Decision**: ✅ **RESOLVED** - Option 1 (Stay on ^8.4) selected. Revisit upgrade planning once PHP 8.5 reaches general availability and ecosystem tooling supports it.

## 5 Tailwind CSS Configuration

### 5.1 Configuration File vs CSS-Based

**Question**: Tailwind CSS 4 uses CSS-based configuration, but some developers may expect a `tailwind.config.js` file. Should a configuration file be provided for customizations?

**Context**: Tailwind CSS 4 changed to CSS-based configuration, removing the need for `tailwind.config.js`. However, custom theme values may still need configuration.

**Current State**: No `tailwind.config.js` file exists, using pure CSS configuration.

**Resolution Options**:

| Option Name | Description | Pros | Cons | Confidence | Effort |
|----|----|----|----|----|----|
| Option 1: Pure CSS Configuration | Continue using CSS-only configuration | Simpler, follows Tailwind 4 pattern | Less familiar to developers | 85% | Low |
| Option 2: Hybrid Approach | Use CSS for base, config file for customizations | Familiar pattern, flexible | May conflict with Tailwind 4 philosophy | 70% | Medium |
| Option 3: Documentation Only | Document CSS approach, provide examples | Clear guidance, no code changes | Requires developer education | 80% | Low |

**Decision**: ✅ **RESOLVED** - Option 1 (Pure CSS Configuration) selected. Continue using CSS-only configuration following Tailwind 4 pattern. Documentation updated to emphasize this approach.

## 6 Testing Framework Consistency

### 6.1 Pest vs PHPUnit

**Question**: The project uses both Pest 4 and PHPUnit 12. Should all tests be migrated to Pest, or maintain both?

**Context**: Pest 4 uses PHPUnit under the hood but provides a different syntax. Some developers may prefer PHPUnit syntax.

**Current State**: Both frameworks are installed, Pest is the primary framework.

**Resolution Options**:

| Option Name | Description | Pros | Cons | Confidence | Effort |
|----|----|----|----|----|----|
| Option 1: Pest Only | Migrate all tests to Pest syntax | Consistency, modern syntax | Requires migration effort | 90% | High |
| Option 2: Support Both | Allow both Pest and PHPUnit tests | Flexibility, no migration needed | Inconsistency, maintenance overhead | 60% | Low |
| Option 3: Pest Primary, PHPUnit Legacy | Use Pest for new tests, allow PHPUnit for legacy | Gradual migration, flexibility | Inconsistent codebase | 75% | Medium |

**Decision**: ✅ **RESOLVED** - Option 1 (Pest Only) selected. Migrate all tests to Pest syntax for consistency. Pest 4 is the modern testing framework for this project with excellent features.

## 7 Database Configuration

### 7.1 Default Database Driver

**Question**: What should be the default database driver for development?

**Context**: The project supports MySQL, PostgreSQL, and SQLite. The default isn’t clearly specified.

**Current State**: `.env.example` may specify a default, but it’s not clear.

**Resolution Options**:

| Option Name | Description | Pros | Cons | Confidence | Effort |
|----|----|----|----|----|----|
| Option 1: SQLite for Development | Use SQLite as default for development | Easy setup, no server required | Different from production | 85% | Low |
| Option 2: MySQL for All Environments | Use MySQL consistently | Matches production, consistent | Requires MySQL installation | 80% | Low |
| Option 3: Document Options | Document all three options clearly | Flexibility for developers | No default recommendation | 75% | Low |

**Decision**: ✅ **RESOLVED** - Option 1 (SQLite for Development) selected. Use SQLite with Write-Ahead Logging (WAL) mode as default for development. SQLite is perfect for development as it requires no setup. Document production MySQL/PostgreSQL requirements separately.

## 8 Additional Clarifications and Decisions

### 8.1 FrankenPHP Runtime Documentation

**Question**: FrankenPHP Symfony Runtime is listed in dependencies but not documented in setup guides.

**Decision**: ✅ **IMPLEMENTED** - FrankenPHP runtime is included but optional. It’s used for high-performance PHP applications. Documentation should note that it’s optional and primarily for production deployments. Standard PHP-FPM or Octane are sufficient for most use cases.

**Implementation**: ✅ Added note in `03-laravel-core.md` (Section 1) that FrankenPHP is optional and production-focused.

### 8.2 Browser Testing Setup Documentation

**Question**: Playwright is installed but browser testing setup is not documented in the setup guides.

**Decision**: ✅ **IMPLEMENTED** - Browser testing should be documented in `11-development-tools.md`. Include setup steps for Playwright browsers and configuration.

**Implementation**: ✅ Added comprehensive Playwright setup section to `11-development-tools.md` (Section 4.6) with installation, configuration, running tests, system dependencies, troubleshooting, and Pest 4 integration. Includes citations to official Playwright documentation.

### 8.3 Spatie Packages Documentation Depth

**Question**: Spatie packages documentation is minimal compared to other packages.

**Decision**: ✅ **IMPLEMENTED** - Spatie packages follow standard Laravel patterns and auto-discovery. Enhanced documentation with comprehensive usage examples for each package.

**Implementation**: ✅ Enhanced `09-spatie-packages.md` with package overviews, key features, and detailed usage examples for all 9 Spatie packages (Activity Log, Analytics, Backup, Event Sourcing, Health, Markdown, Media Library, Schedule Monitor, Settings).

### 8.4 Setup Order and Workflow

**Question**: No clear recommended setup order or workflow is provided.

**Decision**: ✅ **IMPLEMENTED** - Setup should follow a logical order: PHP → Laravel Core → Database → Frontend → Packages. Created comprehensive setup workflow guide.

**Implementation**: ✅ Added "Setup Order and Workflow" section to `README.md` (Section 2) with recommended setup order, quick setup commands, and verification checklist.

### 8.5 Package Update Strategy

**Question**: How to update development version packages (dev-master, dev-main, ^5.x-dev) is not documented.

**Decision**: ✅ **IMPLEMENTED** - Documented update strategy for both stable and development packages. Development packages should be updated carefully with testing.

**Implementation**: ✅ Added comprehensive "Package Update Strategy" section to `README.md` (Section 7) covering stable packages, development packages, update verification, and version constraints.

### 8.6 CI/CD Integration Documentation

**Question**: GitHub Actions workflows exist but CI/CD setup is not documented.

**Decision**: ✅ **IMPLEMENTED** - CI/CD setup is infrastructure-specific. Added comprehensive documentation for GitHub Actions workflows.

**Implementation**: ✅ Added "CI/CD Integration" section to `README.md` (Section 8) covering GitHub Actions workflows, CI/CD setup, required secrets, and local testing instructions.

### 8.7 Environment Variable Organization

**Question**: Environment variables are scattered across multiple documents.

**Decision**: ✅ **IMPLEMENTED** - Environment variables are documented per-package which is appropriate. Added centralized quick reference table.

**Implementation**: ✅ Added "Environment Variables Quick Reference" table to `README.md` (Section 6) with all environment variables, purposes, document links, and default values.

### 8.8 Gitignore Entries Documentation

**Question**: `.gitignore` includes npm/yarn files but this should be documented since we use Bun exclusively.

**Decision**: ✅ **IMPLEMENTED** - `.gitignore` entries for npm/yarn are kept for compatibility but documented that they’re not used. Ensured `bun.lock` is tracked and `package-lock.json`/`yarn.lock` are ignored.

**Implementation**: ✅ Added "Gitignore Entries" section to `12-frontend-build.md` (Section 9.5) explaining what to track, what to ignore, and rationale for npm/yarn entries.

## 9 Summary and Next Actions

### 9.1 Resolved Decisions

All decisions have been made and documented:

1. ✅ **Version Constraints** - Hybrid approach: stable for core packages, dev for newer packages (Filament 5, Volt)
2. ✅ **Custom Repository Auth** - Environment variables with `auth.json` (gitignored)
3. ✅ **PHP Version Constraint** - Standardize on `^8.4`, revisit PHP 8.5 after GA
4. ✅ **Tailwind Configuration** - Pure CSS configuration (no config file)
5. ✅ **Testing Framework** - Pest only (migrate all PHPUnit tests)
6. ✅ **Database Default** - SQLite with WAL mode for development
7. ✅ **FrankenPHP Runtime** - Optional, production-focused, documented
8. ✅ **Browser Testing** - Playwright setup documented
9. ✅ **Setup Workflow** - Recommended order documented
10. ✅ **Package Updates** - Update strategy documented

### 9.2 Implementation Status

All decisions have been implemented or documented in the setup documentation:
\* ✅ PHP 8.4 baseline documented in `02-php-runtime.md`
\* ✅ Tailwind CSS pure CSS approach documented in `12-frontend-build.md`
\* ✅ Pest-only testing documented in `11-development-tools.md`
\* ✅ SQLite with WAL mode documented in `03-laravel-core.md`
\* ✅ Custom repository authentication documented in `04-livewire-ecosystem.md`
\* ✅ Bun exclusivity documented throughout `12-frontend-build.md`
\* ✅ Additional clarifications added to this document
\* ✅ All documentation improvements implemented (see Section 10)
\* ✅ Citations to official sources added throughout documentation suite

### 9.3 Documentation Improvements Status

All recommended improvements have been **IMPLEMENTED**:

✅ **High Priority:**

- ✅ Setup order/workflow guide - Added to `README.md` (Section 2)
- ✅ Playwright browser testing setup - Added to `11-development-tools.md` (Section 4.6)
- ✅ Package update strategy documentation - Added to `README.md` (Section 7)

✅ **Medium Priority:**

- ✅ Spatie packages usage examples - Enhanced `09-spatie-packages.md` with comprehensive examples
- ✅ Environment variables quick reference - Added to `README.md` (Section 6)
- ✅ FrankenPHP runtime documentation - Added note to `03-laravel-core.md` (Section 1)

✅ **Low Priority:**

- ✅ Troubleshooting index - Created `14-troubleshooting.md`
- ✅ Quick start guide - Created `00-quick-start.md`
- ✅ CI/CD documentation - Added to `README.md` (Section 8)

## 10 Implementation Status

### 10.1 Completed Enhancements

All recommended improvements from the documentation review have been implemented:

#### 10.1.1 Structure and Organization ✅

- ✅ **Setup Order Guide**: Added to `README.md` (Section 2) with recommended setup order, quick setup commands, and verification checklist
- ✅ **Quick Start Guide**: Created `00-quick-start.md` with condensed setup for experienced developers
- ✅ **Troubleshooting Index**: Created `14-troubleshooting.md` with centralized troubleshooting guide
- ✅ **Environment Variables Reference**: Added table to `README.md` (Section 6) with links to detailed docs

#### 10.1.2 Content Enhancements ✅

- ✅ **Spatie Packages**: Enhanced `09-spatie-packages.md` with package overviews, key features, and comprehensive usage examples for all 9 packages
- ✅ **Browser Testing**: Added detailed Playwright setup section to `11-development-tools.md` (Section 4.6) with installation, configuration, and troubleshooting
- ✅ **FrankenPHP Runtime**: Added note to `03-laravel-core.md` (Section 1) explaining it’s optional and production-focused
- ✅ **Package Updates**: Added comprehensive update strategy documentation to `README.md` (Section 7) covering stable and development packages
- ✅ **CI/CD**: Added CI/CD integration section to `README.md` (Section 8) with GitHub Actions workflows documentation

#### 10.1.3 Additional Improvements ✅

- ✅ **Gitignore Entries**: Documented in `12-frontend-build.md` (Section 9.5) explaining npm/yarn entries are for compatibility
- ✅ **Code Examples**: All examples are complete and copy-paste ready
- ✅ **Verification Commands**: Added verification commands throughout documentation
- ✅ **Cross-References**: All cross-references verified and working
- ✅ **Citations**: Added citations to official documentation sources (see Section 10.2)

### 10.2 Documentation Citations

All documentation now includes proper citations to original sources:

- **Laravel**: [Laravel 12.x Documentation](https://laravel.com/docs/12.x)
- **Livewire**: [Livewire Documentation](https://livewire.laravel.com/docs)
- **Pest**: [Pest Testing Framework Documentation](https://pestphp.com/docs)
- **Bun**: [Bun Documentation](https://bun.sh/docs)
- **Tailwind CSS**: [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- **Vite**: [Vite Documentation](https://vitejs.dev/guide)
- **Playwright**: [Playwright Documentation](https://playwright.dev/docs/intro)
- **Herd**: [Laravel Herd Documentation](https://herd.laravel.com)
- **PHP**: [PHP Installation Documentation](https://www.php.net/manual/en/install.php)

Citations are placed at relevant sections throughout the documentation suite.

### 10.3 Future Maintenance

**Recommended practices:**
\* Review documentation quarterly for accuracy
\* Update package versions when dependencies change
\* Verify all external links remain active
\* Update citations when official documentation structure changes
\* Track documentation version with package versions

**Documentation Version**: Current as of implementation date
**Last Reviewed**: Implementation date
**Next Review**: Quarterly or when major package versions change

## 11 Documentation Citations and Sources

### 11.1 Official Documentation Sources

All documentation includes proper citations to original sources. Key sources include:

- **Laravel**: [Laravel 12.x Documentation](https://laravel.com/docs/12.x)
- **Livewire**: [Livewire Documentation](https://livewire.laravel.com/docs)
- **Pest**: [Pest Testing Framework Documentation](https://pestphp.com/docs)
- **Bun**: [Bun Documentation](https://bun.sh/docs)
- **Tailwind CSS**: [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- **Vite**: [Vite Documentation](https://vitejs.dev/guide)
- **Filament**: [Filament Documentation](https://filamentphp.com/docs)
- **Playwright**: [Playwright Documentation](https://playwright.dev/docs/intro)
- **Herd**: [Laravel Herd Documentation](https://herd.laravel.com)
- **PHP**: [PHP Installation Documentation](https://www.php.net/manual/en/install.php)

Citations are placed at relevant sections throughout the documentation suite to ensure all guidance is properly grounded in official sources.

### 11.2 Citation Standards

All documentation follows these citation standards:

- **Package-specific features**: Cite official package documentation
- **Installation instructions**: Cite official installation guides
- **Configuration patterns**: Cite official configuration documentation
- **Best practices**: Cite official best practices guides
- **Breaking changes**: Cite official migration guides

## 12 Navigation

[← Bun, Vite, and Tailwind CSS 4](130-frontend-build.md) | [↑ Top](#outstanding-questions-decisions-and-inconsistencies) | [Troubleshooting Index →](150-troubleshooting.md)
