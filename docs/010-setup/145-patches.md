# Composer Patches

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document explains the composer patch system used to maintain compatibility between bleeding-edge package versions in this Laravel application.

## 2 Why Patches?

This project uses development versions (`dev-main`, `^5.x-dev`, etc.) of several packages including:

- Laravel Framework 12.x (latest)
- Livewire 4.0.0-beta.3
- Filament 5.x-dev
- Volt dev-main

These packages are under active development and sometimes have temporary incompatibilities. Rather than waiting for official releases, this project uses composer patches to maintain compatibility.

## 3 Patch Management

### 3.1 Plugin

Patches are managed by the **cweagans/composer-patches** plugin, which automatically applies patches during `composer install` and `composer update`.

**Configuration in `composer.json`:**

``` json
{
  "require": {
    "cweagans/composer-patches": "^1.7"
  },
  "config": {
    "allow-plugins": {
      "cweagans/composer-patches": true
    }
  },
  "extra": {
    "composer-exit-on-patch-failure": true
  }
}
```

### 3.2 Current Patches

#### 3.2.1 Laravel Framework - PDO MySQL SSL Attribute

**File:** `patches/laravel-framework/pdo-mysql-ssl-attr.patch`

**Purpose:** Adds support for `PDO::ATTR_SSL_CA` when available under PHP 8.5.

**Applies to:** `laravel/framework`

**Status:** Required for PHP 8.4+ compatibility

#### 3.2.2 Filament - Livewire v4 ComponentRegistry

**File:** `patches/filament-filament/livewire-v4-compatibility.patch`

**Purpose:** Fixes Livewire v4 compatibility by replacing removed `ComponentRegistry` class with new factory API.

**Applies to:** `filament/filament`

**Status:** Temporary - will be removed when Filament officially supports Livewire v4

**Details:**

- **Problem:** Livewire v4.0.0-beta.3 removed `Livewire\Mechanisms\ComponentRegistry`
- **Error:** `BindingResolutionException: Target class [Livewire\Mechanisms\ComponentRegistry] does not exist`
- **Solution:** Replace `app(ComponentRegistry::class)->getName($component)` with `app('livewire.factory')->resolveComponentName($component)`

**Modified file:** `vendor/filament/filament/src/Panel/Concerns/HasComponents.php`

See [patches/filament-filament/README.md](../../patches/filament-filament/README.md) for full details.

## 4 How Patches Work

### 4.1 During Installation

When you run `composer install` or `composer update`:

1. Composer downloads packages
2. The patches plugin gathers all patches
3. Each patch is applied to its target package
4. If any patch fails, installation stops (due to `composer-exit-on-patch-failure`)

**Example output:**

``` log
Gathering patches for root package.
Gathering patches for dependencies. This might take a minute.
Installing dependencies from lock file
Package operations: 1 install, 0 updates, 0 removals
  - Installing filament/filament (5.x-dev 08bc265): Extracting archive
  - Applying patches for filament/filament
    patches/filament-filament/livewire-v4-compatibility.patch (Fix Livewire v4 ComponentRegistry compatibility)
```

### 4.2 Verification

To verify patches are configured:

``` bash
# Check composer.json for patches section
grep -A 5 '"patches"' composer.json

# List all configured patches
composer show -p
```

## 5 Creating New Patches

If you need to create a new patch:

### 5.1 Make Changes

1. Make changes to the vendor file
2. Test the changes
3. Generate the patch

### 5.2 Generate Patch

``` bash
# Generate diff from git
cd vendor/package/name
git diff path/to/file.php > ../../../patches/package-name/description.patch

# Or use diff directly
diff -u original.php modified.php > patches/package-name/description.patch
```

### 5.3 Configure in composer.json

Add the patch to `composer.json`:

``` json
{
  "extra": {
    "patches": {
      "vendor/package": {
        "Description of the fix": "patches/vendor-package/filename.patch"
      }
    }
  }
}
```

### 5.4 Test the Patch

``` bash
# Remove the package
composer remove vendor/package --no-update

# Reinstall with patch
composer require vendor/package

# Verify patch is applied
grep "expected-change" vendor/package/path/to/file.php
```

## 6 Patch Lifecycle

### 6.1 Temporary Patches

Most patches in this project are **temporary** and should be removed when:

- The package releases an official fix
- The package is updated to a stable version that includes the fix
- The dependency causing the issue is removed

### 6.2 Permanent Patches

Some patches may be **permanent** if they:

- Backport features from newer PHP versions
- Add project-specific customizations
- Fix issues that won't be addressed upstream

### 6.3 Monitoring Patches

Regularly check if patches can be removed:

``` bash
# Check package versions
composer show --latest

# Test without patches (on a separate branch)
# Temporarily remove patches from composer.json
composer update
php artisan test
```

## 7 Troubleshooting

### 7.1 Patch Fails to Apply

**Symptom:** `Could not apply patch! Skipping...` or installation stops

**Causes:**

- Package version changed and patch no longer matches
- Patch file format is incorrect
- File paths in patch are wrong

**Solutions:**

1. Check package version: `composer show package/name`
2. Regenerate the patch for the new version
3. Verify patch file format (should be unified diff)
4. Check file paths in patch header

### 7.2 Patch Applied but Issue Persists

**Symptom:** Patch applies successfully but the fix doesn't work

**Solutions:**

``` bash
# Clear all caches
php artisan optimize:clear

# Verify the patched file
cat vendor/package/path/to/file.php | grep "expected-change"

# Re-apply patches
composer install --no-cache
```

### 7.3 Cannot Remove Package Due to Patch

**Symptom:** Cannot remove or update package because of patch dependency

**Solution:**

1. Remove the patch from `composer.json` first
2. Then remove/update the package

``` bash
# Edit composer.json to remove patch
# Then run:
composer update vendor/package
```

## 8 Best Practices

### 8.1 Documentation

- Always document why a patch exists
- Include the expected removal date or condition
- Link to related issues or PRs

### 8.2 Minimal Changes

- Keep patches as small as possible
- Only change what's necessary to fix the issue
- Avoid reformatting or style changes

### 8.3 Testing

- Test that the patch applies cleanly
- Test that the fix works
- Test that it doesn't break anything else

### 8.4 Version Control

- Store patches in version control
- Include a README in each patch directory
- Use descriptive patch filenames

## 9 Navigation

[← Troubleshooting Index](150-troubleshooting.md) | [↑ Top](#composer-patches) | [Outstanding Questions, Decisions, and Inconsistencies →](140-outstanding-questions.md)
