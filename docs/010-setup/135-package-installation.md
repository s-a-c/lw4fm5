# Composer and JavaScript Package Installation Guide

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

---

<details>
<summary>Expand for Table of Contents</summary>

- [Composer and JavaScript Package Installation Guide](#composer-and-javascript-package-installation-guide)
  - [1 Introduction](#1-introduction)
  - [2 Prerequisites Checklist](#2-prerequisites-checklist)
    - [2.1 System Requirements](#21-system-requirements)
    - [2.2 PHP Installation](#22-php-installation)
    - [2.3 Composer Installation](#23-composer-installation)
    - [2.4 Bun Installation](#24-bun-installation)
    - [2.5 Git Installation](#25-git-installation)
  - [3 Composer Package Configuration](#3-composer-package-configuration)
    - [3.1 Custom Repository Setup](#31-custom-repository-setup)
    - [3.2 Authentication Configuration (Flux Pro)](#32-authentication-configuration-flux-pro)
    - [3.3 Production Dependencies Installation](#33-production-dependencies-installation)
    - [3.4 Development Dependencies Installation](#34-development-dependencies-installation)
    - [3.5 Post-Installation Tasks](#35-post-installation-tasks)
    - [3.6 Verification Steps](#36-verification-steps)
  - [4 JavaScript Package Configuration](#4-javascript-package-configuration)
    - [4.1 Bun Setup and Verification](#41-bun-setup-and-verification)
    - [4.2 Package.json Configuration](#42-packagejson-configuration)
    - [4.3 Dependency Installation](#43-dependency-installation)
    - [4.4 Optional Dependencies](#44-optional-dependencies)
    - [4.5 Build System Verification](#45-build-system-verification)
    - [4.6 Testing Dependencies Setup](#46-testing-dependencies-setup)
    - [4.7 Verification Steps](#47-verification-steps)
  - [5 Comprehensive Verification](#5-comprehensive-verification)
    - [5.1 Complete Installation Checklist](#51-complete-installation-checklist)
    - [5.2 Application Startup Test](#52-application-startup-test)
    - [5.3 Frontend Build Test](#53-frontend-build-test)
    - [5.4 Test Suite Execution](#54-test-suite-execution)
    - [5.5 Package Health Check](#55-package-health-check)
  - [6 Troubleshooting](#6-troubleshooting)
    - [6.1 Common Installation Errors](#61-common-installation-errors)
      - [Problem: "Your requirements could not be resolved"](#problem-your-requirements-could-not-be-resolved)
      - [Problem: "Out of memory" Error](#problem-out-of-memory-error)
      - [Problem: "Class not found" After Installation](#problem-class-not-found-after-installation)
    - [6.2 Authentication Issues](#62-authentication-issues)
      - [Problem: Flux Pro Authentication Fails](#problem-flux-pro-authentication-fails)
      - [Problem: auth.json Not Being Used](#problem-authjson-not-being-used)
    - [6.3 Platform-Specific Issues](#63-platform-specific-issues)
      - [Problem: Optional Dependencies Not Installing (Windows)](#problem-optional-dependencies-not-installing-windows)
      - [Problem: Permission Denied Errors (Linux/macOS)](#problem-permission-denied-errors-linuxmacos)
    - [6.4 Build Failures](#64-build-failures)
      - [Problem: Vite Build Fails](#problem-vite-build-fails)
      - [Problem: Tailwind CSS Not Working](#problem-tailwind-css-not-working)
    - [6.5 Version Compatibility Issues](#65-version-compatibility-issues)
      - [Problem: Package Version Conflicts](#problem-package-version-conflicts)
      - [Problem: PHP Version Mismatch](#problem-php-version-mismatch)
  - [7 Next Steps](#7-next-steps)
  - [8 Navigation](#8-navigation)

</details>

---

## 1 Introduction

This guide provides a comprehensive, step-by-step walkthrough for installing and configuring all Composer (PHP) and JavaScript packages required for the Laravel Livewire Starter Kit. This document is designed for junior developers (6 months - 2 years experience) and ensures your development environment is fully configured and ready for feature development.

**What You'll Learn:**

- How to verify all prerequisites are installed
- How to configure custom Composer repositories
- How to authenticate with private package repositories (Flux Pro)
- How to install production and development dependencies
- How to verify installations are working correctly
- How to troubleshoot common installation issues

**Estimated Time:** 15-30 minutes (depending on internet speed and system performance)

**Important Notes:**

- This project uses **Bun exclusively** for JavaScript package management. Never use `npm`, `npx`, or `yarn`.
- Some packages require authentication (Flux Pro) before installation.
- Always verify each step before proceeding to the next one.

---

## 2 Prerequisites Checklist

Before installing packages, ensure all required tools are installed and properly configured.

### 2.1 System Requirements

The project requires the following minimum versions:

| Tool | Minimum Version | Recommended Version | Purpose |
|------|----------------|---------------------|---------|
| PHP | 8.4.0 | 8.4.14 | PHP runtime |
| Composer | 2.0.0 | 2.x (latest) | PHP package manager |
| Bun | 1.1.0 | 1.1.17+ | JavaScript package manager |
| Git | 2.0.0 | Latest | Version control |
| Node.js | 25.0.0 | 25+ | Required for Bun (runtime) |

### 2.2 PHP Installation

**Step 1: Verify PHP Installation**

```bash
php -v
```

**Expected Output:**

```log
PHP 8.4.14 (cli) (built: ...) (NTS)
Copyright (c) The PHP Group
...
```

**Step 2: Verify Required PHP Extensions**

```bash
php -m | grep -E "bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml"
```

**Expected Output:** All extensions should be listed. If any are missing, install them using your system's package manager.

**Verification:** ✅ PHP 8.4+ installed and required extensions available

### 2.3 Composer Installation

**Step 1: Verify Composer Installation**

```bash
composer --version
```

**Expected Output:**

```log
Composer version 2.x.x ...
```

**Step 2: Verify Composer Configuration**

```bash
composer --ansi
```

**Expected Output:** No errors, Composer responds with help text.

**Installation Help:** If Composer is not installed, follow the [official installation guide](https://getcomposer.org/download/).

**Verification:** ✅ Composer 2.x installed and working

### 2.4 Bun Installation

**Step 1: Verify Bun Installation**

```bash
bun --version
```

**Expected Output:**

```log
1.1.17
```

Or similar version 1.1.0 or higher.

**Step 2: Verify Bun Package Manager**

```bash
bun install --help
```

**Expected Output:** Help text displays showing Bun package manager commands.

**Installation Help:** If Bun is not installed, follow the [official installation guide](https://bun.sh/docs/installation).

> [!IMPORTANT]
> This project uses **Bun exclusively**. Never use `npm`, `npx`, or `yarn` commands. Always use `bun` instead of `npm` and `bunx` instead of `npx`.

**Verification:** ✅ Bun 1.1.0+ installed and working

### 2.5 Git Installation

**Step 1: Verify Git Installation**

```bash
git --version
```

**Expected Output:**

```log
git version 2.x.x
```

**Verification:** ✅ Git installed and working

---

## 3 Composer Package Configuration

This section covers installing all PHP packages managed by Composer.

### 3.1 Custom Repository Setup

The project uses custom Composer repositories for private and special packages. These are already configured in `composer.json`, but you need to understand what they are:

**Custom Repositories:**

| Repository Name | URL | Purpose | Authentication Required |
|----------------|-----|---------|------------------------|
| `fluxui-pro` | `https://composer.fluxui.dev` | Flux Pro premium components | ✅ Yes |
| `laravel-comments` | `https://satis.spatie.be` | Spatie private packages | ❌ No |
| `laravel-labs-starter-kit-browser-tests` | GitHub VCS | Browser test utilities | ❌ No |

**Verification:** Check that repositories are configured:

```bash
composer config repositories --list
```

**Expected Output:** All three repositories should be listed.

### 3.2 Authentication Configuration (Flux Pro)

Flux Pro requires authentication before installation. Follow these steps carefully:

**Step 1: Check if auth.json Already Exists**

```bash
ls -la auth.json 2>/dev/null || echo "auth.json does not exist yet"
```

**Step 2: Verify auth.json is Gitignored**

```bash
grep -q "^/auth.json$" .gitignore && echo "✅ auth.json is gitignored" || echo "⚠️  auth.json should be gitignored"
```

The file should already be in `.gitignore` (line 18). If not, add it manually:

```bash
echo "/auth.json" >> .gitignore
```

**Step 3: Get Flux Pro Credentials**

You need:

- **Username**: Your Flux Pro account email
- **Password**: Your Flux Pro license key

These can be found on your [Flux Pro dashboard](https://fluxui.dev/dashboard).

**Step 4: Set Environment Variables**

Add these to your `.env` file (create it from `.env.example` if needed):

```bash
# If .env doesn't exist, create it
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Add Flux Pro credentials to .env
# Edit .env and add:
# FLUXUI_PRO_USERNAME=your-email@example.com
# FLUXUI_PRO_KEY=your-license-key
```

**Step 5: Create auth.json File**

Create the `auth.json` file with your credentials:

**Method 1: Using Composer Config Command (Recommended)**

```bash
composer config http-basic.composer.fluxui.dev "${FLUXUI_PRO_USERNAME}" "${FLUXUI_PRO_KEY}"
```

**Method 2: Manual File Creation**

If environment variables aren't available, create `auth.json` manually:

```json
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "your-email@example.com",
            "password": "your-license-key"
        }
    }
}
```

> [!WARNING]
> Never commit `auth.json` to version control. It should always be gitignored.

**Step 6: Verify Authentication**

```bash
composer show livewire/flux-pro 2>&1 | head -5
```

**Expected Output:** Package information displays without authentication errors.

**Verification:** ✅ Flux Pro authentication configured

> [!NOTE]
> If you encounter authentication errors, see [Section 6.2](#62-authentication-issues) for troubleshooting.

### 3.3 Production Dependencies Installation

**Step 1: Navigate to Project Root**

```bash
cd /path/to/your/project
```

Or if you're already in the project directory:

```bash
pwd  # Verify you're in the correct directory
```

**Step 2: Install Production Dependencies**

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

**What This Command Does:**

- `--no-interaction`: Prevents interactive prompts (required for automation)
- `--prefer-dist`: Prefers stable package distributions over source
- `--optimize-autoloader`: Optimizes the autoloader for better performance

**Expected Output:**

```log
Loading composer repositories with package information
Gathering patches for root package.
Gathering patches for dependencies. This might take a minute.
Installing dependencies from lock file (if composer.lock exists)
or
Updating dependencies (if no composer.lock exists)
...
  - Applying patches for filament/filament
    patches/filament-filament/livewire-v4-compatibility.patch (Fix Livewire v4 ComponentRegistry compatibility)
...
Writing lock file
Generating optimized autoload files
```

> [!NOTE]
> **About Patches**: This project uses composer patches to fix compatibility issues between bleeding-edge versions. You'll see patches being applied during installation - this is normal and expected. The patches are managed via the `cweagans/composer-patches` plugin.

**Step 3: Monitor Installation Progress**

Watch for any errors, especially:

- Authentication failures (should not occur if Step 3.2 was completed)
- Missing PHP extensions
- Memory limit errors

**Verification:** ✅ Production dependencies installed

**Troubleshooting:** If installation fails, see [Section 6.1](#61-common-installation-errors).

### 3.4 Development Dependencies Installation

Development dependencies are installed automatically when you run `composer install` (unless you use `--no-dev` flag).

**Step 1: Verify Development Dependencies Were Installed**

```bash
composer show --dev | head -20
```

**Expected Output:** Lists development packages including Pest, Larastan, Rector, etc.

**Step 2: Check for Key Development Tools**

```bash
php artisan --version  # Laravel Artisan
vendor/bin/pest --version  # Pest testing framework
vendor/bin/pint --version  # Laravel Pint code formatter
```

**Expected Output:** All commands should show version information.

**Verification:** ✅ Development dependencies installed

> [!TIP]
> To install only production dependencies (for deployment), use:
> ```bash
> composer install --no-dev --optimize-autoloader
> ```

### 3.5 Post-Installation Tasks

After installing Composer packages, complete these tasks:

**Step 1: Run Package Discovery**

```bash
php artisan package:discover --ansi
```

**Expected Output:**

```
Discovered Package: filament/filament
Discovered Package: livewire/livewire
...
```

**Step 2: Run Filament Upgrade (if Filament is installed)**

```bash
php artisan filament:upgrade
```

This command updates Filament assets and configurations.

**Step 3: Clear Configuration Cache**

```bash
php artisan config:clear
```

**Verification:** ✅ Post-installation tasks completed

### 3.6 Verification Steps

**Step 1: Verify Key Packages Are Installed**

```bash
# Check Laravel Framework
composer show laravel/framework

# Check Livewire
composer show livewire/livewire

# Check Flux Pro (if using)
composer show livewire/flux-pro

# Check Pest (dev dependency)
composer show pestphp/pest --dev
```

**Expected Output:** All packages show version information.

**Step 2: Verify Package Autoloading**

```bash
php -r "require 'vendor/autoload.php'; echo 'Autoloader working';"
```

**Expected Output:** `Autoloader working`

**Step 3: Check for Missing Dependencies**

```bash
composer validate
```

**Expected Output:** `./composer.json is valid`

**Step 4: Verify Vendor Directory**

```bash
ls -la vendor/ | head -10
```

**Expected Output:** Vendor directory exists and contains package directories.

**Final Verification:** ✅ All Composer packages installed and verified

---

## 4 JavaScript Package Configuration

This section covers installing all JavaScript/TypeScript packages managed by Bun.

### 4.1 Bun Setup and Verification

**Step 1: Verify Bun Installation (Already Done in Prerequisites)**

```bash
bun --version
```

Should show version 1.1.0 or higher.

**Step 2: Verify Node.js Compatibility**

Bun requires Node.js 25+ (as specified in `package.json`):

```bash
node --version 2>/dev/null || echo "Node.js not required (Bun includes runtime)"
```

Bun includes its own JavaScript runtime, so Node.js is optional but may be required by some tools.

**Step 3: Verify Bun Can Execute Scripts**

```bash
bun run --help
```

**Expected Output:** Help text displays showing available scripts.

**Verification:** ✅ Bun is properly configured

### 4.2 Package.json Configuration

**Step 1: Verify package.json Exists**

```bash
cat package.json | head -20
```

**Expected Output:** Shows package.json contents including scripts, dependencies, etc.

**Step 2: Check Package Manager Configuration**

```bash
grep -A 2 '"packageManager"' package.json
```

**Expected Output:**

```json
"packageManager": "bun@latest",
```

**Step 3: Verify Engine Requirements**

```bash
grep -A 3 '"engines"' package.json
```

**Expected Output:**

```json
"engines": {
    "node": ">=25",
    "bun": ">=1.1.0"
}
```

**Verification:** ✅ package.json properly configured

### 4.3 Dependency Installation

**Step 1: Navigate to Project Root**

```bash
cd /path/to/your/project
```

**Step 2: Install All Dependencies**

```bash
bun install
```

**What This Command Does:**

- Installs all dependencies from `package.json`
- Creates or updates `bun.lock` file
- Installs production and development dependencies
- Handles platform-specific optional dependencies automatically

**Expected Output:**

```log
bun install v1.1.17 (a1b2c3d4)
...
✨  XX packages installed
```

**Step 3: Monitor Installation Progress**

Watch for:

- Network errors
- Permission errors
- Missing optional dependencies (warnings are okay)

**Verification:** ✅ JavaScript dependencies installed

> [!IMPORTANT]
> Always use `bun install` instead of `npm install`. Using npm may cause lock file conflicts.

### 4.4 Optional Dependencies

The project includes platform-specific optional dependencies that Bun will install automatically for your platform:

| Package | Purpose | Platforms |
|---------|---------|-----------|
| `@rollup/rollup-*` | Rollup bundler binaries | Platform-specific |
| `@tailwindcss/oxide-*` | Tailwind CSS engine | Platform-specific |
| `lightningcss-*` | Lightning CSS binary | Platform-specific |

**Step 1: Verify Optional Dependencies**

```bash
bun pm ls | grep -E "rollup|tailwindcss|lightningcss" | head -5
```

**Expected Output:** Shows installed optional dependencies for your platform.

**Note:** It's normal if optional dependencies for other platforms are not installed. Bun only installs what's needed for your current platform.

**Verification:** ✅ Optional dependencies installed for your platform

### 4.5 Build System Verification

**Step 1: Verify Vite Configuration**

```bash
cat vite.config.js 2>/dev/null || cat vite.config.ts 2>/dev/null || echo "Vite config not found (may be in package.json)"
```

**Expected Output:** Vite configuration file exists or is embedded in package.json.

**Step 2: Verify Tailwind CSS Configuration**

```bash
cat tailwind.config.js 2>/dev/null || grep -q "@tailwindcss" package.json && echo "Tailwind CSS v4 (CSS-based config)" || echo "Tailwind config not found"
```

**Expected Output:** Tailwind CSS is configured (either via config file or CSS-based in v4).

**Step 3: Test Build Process**

```bash
bun run build
```

**Expected Output:**

```log
vite v7.x.x building for production...
✓ XX modules transformed.
dist/index.html                    XX kB
...
built in XXXms
```

**Step 4: Verify Build Output**

```bash
ls -la public/build/ 2>/dev/null | head -10 || echo "Build directory not found (may be first build)"
```

**Expected Output:** Build artifacts are created in `public/build/` directory.

**Verification:** ✅ Build system working correctly

> [!TIP]
> For development, use `bun run dev` instead of `bun run build`. The dev command starts a development server with hot module replacement.

### 4.6 Testing Dependencies Setup

**Step 1: Verify Playwright is Installed**

```bash
bunx playwright --version
```

**Expected Output:**

```log
Version 1.56.1
```

Or similar version number.

**Step 2: Install Playwright Browsers (Required for Browser Tests)**

```bash
bunx playwright install --with-deps
```

**What This Command Does:**

- Downloads browser binaries (Chromium, Firefox, WebKit)
- Installs system dependencies required by browsers
- Can take several minutes depending on internet speed

**Expected Output:**

```log
Downloading Chromium XXXX...
Installing system dependencies...
...
XX browsers downloaded
```

**Step 3: Verify Browser Installation**

```bash
bunx playwright install chromium  # Test specific browser
```

**Expected Output:** Browser already installed or downloads successfully.

**Verification:** ✅ Testing dependencies ready

> [!NOTE]
> Browser installation is only required if you plan to run browser tests. You can skip this step if you only need unit/feature tests.

### 4.7 Verification Steps

**Step 1: Verify Key Packages Are Installed**

```bash
# Check Tailwind CSS
bun pm ls tailwindcss

# Check Vite
bun pm ls vite

# Check Laravel Vite Plugin
bun pm ls laravel-vite-plugin

# Check Playwright (dev dependency)
bun pm ls playwright --dev
```

**Expected Output:** All packages show version information.

**Step 2: Verify Node Modules Directory**

```bash
ls -la node_modules/ | head -10
```

**Expected Output:** Node modules directory exists and contains package directories.

**Step 3: Check for Lock File**

```bash
ls -lh bun.lock
```

**Expected Output:** Lock file exists with reasonable size (should not be empty).

**Step 4: Verify Scripts Work**

```bash
bun run --help
```

Should list all available scripts from package.json.

**Final Verification:** ✅ All JavaScript packages installed and verified

---

## 5 Comprehensive Verification

After completing both Composer and JavaScript package installation, perform these comprehensive checks.

### 5.1 Complete Installation Checklist

Use this checklist to verify everything is installed:

**Composer Packages:**

- [ ] Laravel Framework installed
- [ ] Livewire installed
- [ ] Flux Pro installed (if using)
- [ ] Filament installed
- [ ] Pest installed (dev)
- [ ] All packages from `composer.json` installed
- [ ] Vendor directory exists with packages

**JavaScript Packages:**

- [ ] Tailwind CSS installed
- [ ] Vite installed
- [ ] Laravel Vite Plugin installed
- [ ] All packages from `package.json` installed
- [ ] Node modules directory exists with packages
- [ ] Bun lock file exists

**Configuration:**

- [ ] `.env` file exists
- [ ] `auth.json` exists (for Flux Pro)
- [ ] `auth.json` is gitignored
- [ ] Application key generated (if starting fresh)

### 5.2 Application Startup Test

**Step 1: Clear All Caches**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Step 2: Generate Application Key (if not already done)**

```bash
php artisan key:generate
```

**Step 3: Test Laravel Application**

```bash
php artisan about
```

**Expected Output:**

```log
Application Information
======================

Environment:              local
Debug Mode:               Yes
URL:                      http://localhost
...
Laravel Version:          12.38.1
PHP Version:              8.4.14
...
```

**Step 4: Start Development Server (Optional Test)**

```bash
php artisan serve
```

**Expected Output:**

```log
INFO  Server running on [http://127.0.0.1:8000]

  Press Ctrl+C to stop the server
```

Press `Ctrl+C` to stop the server.

**Verification:** ✅ Laravel application starts successfully

### 5.3 Frontend Build Test

**Step 1: Clean Previous Builds**

```bash
rm -rf public/build public/hot
```

**Step 2: Run Production Build**

```bash
bun run build
```

**Expected Output:** Build completes without errors, assets created in `public/build/`.

**Step 3: Verify Build Assets**

```bash
ls -lh public/build/assets/ 2>/dev/null | head -5
```

**Expected Output:** Compiled JavaScript and CSS files exist.

**Verification:** ✅ Frontend build process working

### 5.4 Test Suite Execution

**Step 1: Run All Tests**

```bash
php artisan test
```

**Expected Output:**

```log
PEST
  PHP: 8.4.14
  ...

  Tests:  XX passed
  Duration:  X.XXs
```

**Step 2: Run Type Coverage Check**

```bash
composer test:type-coverage
```

**Expected Output:** Type coverage report shows 100% or acceptable coverage.

**Step 3: Run Code Style Check**

```bash
vendor/bin/pint --test
```

**Expected Output:** All files pass code style checks (or shows files that need formatting).

**Verification:** ✅ Test suite executes successfully

> [!NOTE]
> Some tests may fail if environment is not fully configured (database, etc.). This is expected and those can be configured separately.

### 5.5 Package Health Check

**Step 1: Check for Outdated Packages (Composer)**

```bash
composer outdated
```

Review the output to see if any packages have updates available (optional check).

**Step 2: Check for Vulnerabilities**

```bash
composer audit
```

**Expected Output:** No vulnerabilities found, or a list of known vulnerabilities with severity ratings.

**Step 3: Verify Package Integrity (Bun)**

```bash
bun install --check
```

**Expected Output:** All dependencies are installed and match lock file.

**Verification:** ✅ Package health verified

---

## 6 Troubleshooting

This section addresses common issues you may encounter during package installation.

### 6.1 Common Installation Errors

#### Problem: "Your requirements could not be resolved"

**Symptoms:**

```log
Your requirements could not be resolved to an installable set of packages.
```

**Solutions:**

1. **Clear Composer Cache:**

   ```bash
   composer clear-cache
   ```

2. **Remove Composer Lock File (use with caution):**

   ```bash
   rm composer.lock
   composer update
   ```

3. **Check PHP Version:**

   ```bash
   php -v  # Should be 8.4+
   ```

4. **Verify composer.json is valid:**

   ```bash
   composer validate
   ```

#### Problem: "Out of memory" Error

**Symptoms:**

```log
Fatal error: Allowed memory size of XXX bytes exhausted
```

**Solutions:**

1. **Increase PHP Memory Limit:**

   ```bash
   php -d memory_limit=2G composer install
   ```

2. **Or set in php.ini:**

   ```ini
   memory_limit = 2G
   ```

#### Problem: "Class not found" After Installation

**Symptoms:**

```log
Class 'SomeClass' not found
```

**Solutions:**

1. **Regenerate Autoloader:**

   ```bash
   composer dump-autoload
   ```

2. **Clear All Caches:**

   ```bash
   php artisan optimize:clear
   ```

### 6.2 Authentication Issues

#### Problem: Flux Pro Authentication Fails

**Symptoms:**

```log
[Composer\Downloader\TransportException]
The "https://composer.fluxui.dev/packages.json" file could not be downloaded (HTTP/1.1 401 Unauthorized)
```

**Solutions:**

1. **Verify auth.json Exists:**

   ```bash
   cat auth.json
   ```

2. **Verify Credentials Format:**

   ```json
   {
       "http-basic": {
           "composer.fluxui.dev": {
               "username": "your-email@example.com",
               "password": "your-license-key"
           }
       }
   }
   ```

3. **Reconfigure Authentication:**

   ```bash
   composer config http-basic.composer.fluxui.dev --unset
   composer config http-basic.composer.fluxui.dev "your-email@example.com" "your-license-key"
   ```

4. **Verify Credentials Are Correct:**
   - Check [Flux Pro dashboard](https://fluxui.dev/dashboard) for correct email and license key
   - Ensure there are no extra spaces in credentials

5. **Test Authentication Manually:**

   ```bash
   curl -u "your-email@example.com:your-license-key" https://composer.fluxui.dev/packages.json | head -20
   ```

**Verification:** Should return JSON instead of 401 error.

#### Problem: auth.json Not Being Used

**Symptoms:** Authentication errors even though auth.json exists.

**Solutions:**

1. **Check File Permissions:**

   ```bash
   ls -la auth.json
   ```

   Should be readable by current user.

2. **Verify File Location:**

   ```bash
   pwd
   cat auth.json

   ```log
   File should be in project root (same directory as composer.json).

3. **Try Global Configuration (Alternative):**

   ```bash
   composer config --global http-basic.composer.fluxui.dev "your-email" "your-key"
   ```

### 6.3 Platform-Specific Issues

#### Problem: Optional Dependencies Not Installing (Windows)

**Symptoms:** Platform-specific packages fail to install on Windows.

**Solutions:**

1. **Install Build Tools:**
   - Install [Visual Studio Build Tools](https://visualstudio.microsoft.com/downloads/#build-tools-for-visual-studio-2022)
   - Install Python (required for some native modules)

2. **Use WSL (Recommended for Windows):**
   - Install Windows Subsystem for Linux
   - Run Bun commands in WSL environment

#### Problem: Permission Denied Errors (Linux/macOS)

**Symptoms:**

```log
EACCES: permission denied
```

**Solutions:**

1. **Fix Node Modules Permissions:**

   ```bash
   sudo chown -R $(whoami) node_modules
   ```

2. **Fix Vendor Permissions:**

   ```bash
   sudo chown -R $(whoami) vendor
   ```

3. **Never Use Sudo for Package Installation:**
   - Don't run `sudo bun install` or `sudo composer install`
   - Fix permissions instead

### 6.4 Build Failures

#### Problem: Vite Build Fails

**Symptoms:**

```log
[vite:resolve] Failed to resolve entry for package "xxx"
```

**Solutions:**

1. **Clear Build Cache:**

   ```bash
   rm -rf node_modules/.vite
   bun run build
   ```

2. **Reinstall Dependencies:**

   ```bash
   rm -rf node_modules bun.lock
   bun install
   bun run build
   ```

3. **Check Vite Configuration:**

   ```bash
   cat vite.config.js
   ```

   Verify Laravel Vite plugin is configured correctly.

#### Problem: Tailwind CSS Not Working

**Symptoms:** Styles not applying, Tailwind classes not working.

**Solutions:**

1. **Verify Tailwind CSS is Installed:**

   ```bash
   bun pm ls tailwindcss
   ```

2. **Check CSS Import:**

   ```bash
   cat resources/css/app.css | head -10
   ```

   Should import Tailwind directives.

3. **Rebuild Assets:**

   ```bash
   bun run build
   ```

4. **Clear Browser Cache:** Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R).

### 6.5 Version Compatibility Issues

#### Problem: Package Version Conflicts

**Symptoms:**

```log
Package XXX requires YYY ^1.0 but you have YYY ^2.0 installed
```

**Solutions:**

1. **Update All Packages:**

   ```bash
   composer update
   bun update
   ```

2. **Check Package Documentation:**
   - Review package changelogs for breaking changes
   - Verify version constraints in composer.json/package.json

3. **Lock Specific Versions (Last Resort):**
   Edit composer.json or package.json to lock specific versions, then update.

#### Problem: PHP Version Mismatch

**Symptoms:** Composer complains about PHP version requirements.

**Solutions:**

1. **Verify PHP Version:**

   ```bash
   php -v
   ```

2. **Check Composer PHP Version:**

   ```bash
   composer --version
   composer config platform.php
   ```

3. **Update PHP (if needed):**
   - Use your system's package manager to upgrade PHP
   - Or use a PHP version manager (like phpbrew or phpenv)

---

## 7 Next Steps

After successfully installing all packages, proceed with:

1. **Environment Configuration:**
   - Configure database connection (see [Laravel Core Setup](040-laravel-core.md))
   - Set up application-specific environment variables
   - Generate application key (`php artisan key:generate`)

2. **Database Setup:**
   - Run migrations (`php artisan migrate`)
   - Seed database if needed (`php artisan db:seed`)

3. **Frontend Development:**
   - Start development server (`bun run dev`)
   - Start Laravel server (`php artisan serve`)
   - Access application at `http://localhost:8000`

4. **Development Tools:**
   - Configure IDE/editor for Laravel and Livewire
   - Set up debugging tools
   - Configure code quality tools (Pint, Rector)

5. **Testing:**
   - Write and run tests (`php artisan test`)
   - Set up continuous integration (CI/CD)

For detailed information on each of these topics, see the corresponding documentation files in the `docs/010-setup/` directory.

---

## 8 Navigation

[← Frontend Build (Bun, Vite, and Tailwind CSS 4)](130-frontend-build.md) | [↑ Top](#composer-and-javascript-package-installation-guide) | [Outstanding Questions, Decisions, and Inconsistencies →](140-outstanding-questions.md)
