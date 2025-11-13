# PHP Runtime Setup

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers PHP 8.4+ runtime requirements, installation, and configuration for the Laravel Livewire Starter Kit.

## 2 PHP Version Requirements

### 2.1 Minimum Version

The project requires PHP 8.4 or higher as specified in `composer.json`:

``` json
"php": "^8.4"
```

### 2.2 Recommended Version

PHP 8.4.12 is the recommended version for this project. PHP 8.4 provides the latest language features, performance improvements, and is required by the project’s dependency constraints.

### 2.3 PHP 8.4 Features Used

The project leverages modern PHP 8.x language features. Ensure your PHP 8.4 installation includes:

- **Readonly classes and properties**: Immutable data structures
- **Intersection and union types**: Precise type declarations
- **Enum improvements**: Native enumerations for domain modeling
- **First-class callable syntax**: Cleaner callback handling
- **Performance optimizations**: Latest Just-In-Time (JIT) improvements

> [!NOTE]
> PHP 8.4 or higher is required. Older versions will not satisfy the `^8.4` constraint in `composer.json`.

## 3 Installation Methods

### 3.1 Using Herd (Recommended for macOS)

Herd is Laravel’s official development environment for macOS:

``` bash
# Install Herd
curl -s https://herd.laravel.com/install.sh | bash

# Verify installation
herd --version
```

Herd automatically manages PHP versions and includes PHP 8.4+.

**Source**: [Laravel Herd Documentation](https://herd.laravel.com)

### 3.2 Using Homebrew (macOS/Linux)

``` bash
# Install PHP 8.4
brew install php@8.4

# Link PHP
brew link php@8.4

# Verify installation
php -v
```

**Source**: [PHP Installation Documentation](https://www.php.net/manual/en/install.php)

### 3.3 Using Laravel Sail (Docker)

Laravel Sail provides a Docker-based development environment:

``` bash
# Install Sail dependencies
composer require laravel/sail --dev

# Publish Sail configuration
php artisan sail:install

# Start containers
./vendor/bin/sail up -d
```

### 3.4 Manual Installation

For manual installation, refer to your operating system’s documentation:

- **Ubuntu/Debian**: Use `ppa:ondrej/php` repository
- **Windows**: Use XAMPP, WAMP, or download from php.net
- **Other Linux**: Use distribution package manager

## 4 Required PHP Extensions

### 4.1 Core Extensions

The following extensions are required for Laravel:

``` php

* php-mbstring    - Multibyte string handling
* php-xml         - XML parsing
* php-xmlreader   - XML reading
* php-dom         - DOM manipulation
* php-curl        - HTTP client
* php-zip         - Archive handling
* php-gd          - Image processing
* php-pdo         - Database abstraction
* php-pdo_mysql   - MySQL support
* php-pdo_pgsql   - PostgreSQL support (optional)
* php-pdo_sqlite  - SQLite support
* php-openssl     - Cryptography
* php-json        - JSON handling

```

### 4.2 Recommended Extensions

Additional extensions for enhanced functionality:

``` php

* php-redis       - Redis support (for queues/cache)
* php-imagick      - Advanced image processing
* php-intl        - Internationalization
* php-bcmath      - Arbitrary precision mathematics

```

### 4.3 Verifying Extensions

``` bash
# Check installed extensions
php -m

# Check specific extension
php -m | grep redis
```

## 5 Configuration Steps

| Step | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
| --- | --- | --- | --- | --- |
| 1 | Verify PHP version | `php -v` | PHP 8.4.x or higher displayed | Check version number in output |
| 2 | Check required extensions | `php -m` | All required extensions listed | Compare with required list above |
| 3 | Configure php.ini (if needed) | Edit `php.ini` | Extensions enabled, limits adjusted | `php --ini` to locate file |
| 4 | Set memory limit | `memory_limit = 512M` in `php.ini` | Higher memory available for large operations | `php -i \| grep memory_limit` |
| 5 | Set execution time | `max_execution_time = 300` in `php.ini` | Longer execution time for commands | `php -i \| grep max_execution_time` |
| 6 | Enable OPcache (production) | `opcache.enable=1` in `php.ini` | Improved performance | `php -i \| grep opcache` |

## 6 Environment Configuration

### 6.1 Composer Configuration

Composer uses PHP from your PATH. Verify Composer is using the correct PHP:

``` bash
# Check Composer PHP version
composer --version

# If incorrect, specify PHP path
composer config --global php /usr/bin/php8.4
```

### 6.2 IDE Configuration

Configure your IDE to use PHP 8.4+:

- **PhpStorm**: Settings → Languages & Frameworks → PHP → CLI Interpreter
- **VS Code**: Install PHP Intelephense extension, configure PHP executable path
- **Other IDEs**: Refer to IDE-specific documentation

## 7 Strict Types Declaration

All PHP files in this project must start with:

``` php
<?php

declare(strict_types=1);
```

This ensures type safety throughout the codebase.

## 8 Verification

### 8.1 Quick Verification

``` bash
# Check PHP version
php -v

# Check Composer
composer --version

# Verify extensions
php -m | grep -E "(mbstring|xml|curl|zip|gd|pdo)"
```

### 8.2 Common Issues

#### 8.2.1 Wrong PHP Version

**Symptom**: Composer reports PHP version mismatch

**Solution**:

``` bash
# Check which PHP is being used
which php

# Update PATH to prioritize correct PHP version
export PATH="/usr/local/opt/php@8.4/bin:$PATH"
```

#### 8.2.2 Missing Extensions

**Symptom**: Laravel installation fails with missing extension error

**Solution**: Install missing extension using your package manager:

``` bash
# macOS (Homebrew)
brew install php@8.4-mbstring

# Ubuntu/Debian
sudo apt-get install php8.4-mbstring
```

## 9 Next Steps

After verifying PHP 8.4+ is installed correctly:

- [laravel-core.md](040-laravel-core.md) - Configure Laravel Framework 12.x

## 10 Navigation

[← Project Overview and Architecture](020-overview.md) | [↑ Top](#php-runtime-setup) | [Laravel Framework 12.x Core Setup →](040-laravel-core.md)
