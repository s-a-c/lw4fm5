# Quick Start Guide

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This is a condensed setup guide for experienced developers. For detailed documentation, see [README.md](README.md).

## 2 Prerequisites

- PHP 8.4+ (8.4.12 recommended)
- Composer 2.x
- Bun 1.1.0+
- Git

## 3 Installation

### 3.1 Clone and Install

``` bash
# Clone the repository
git clone <repository-url>
cd <project-directory>

# Install PHP dependencies
composer install

# Install frontend dependencies
bun install
```

### 3.2 Environment Setup

``` bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database (development)
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 3.3 Frontend Build

``` bash
# Build assets
bun run build

# Or start dev server
bun run dev
```

### 3.4 Install Playwright (for testing)

``` bash
bunx playwright install --with-deps
```

### 3.5 Configure Flux Pro (if using)

``` bash
# Create auth.json (gitignored)
touch auth.json

# Add credentials (see 04-livewire-ecosystem.md for details)
# Set FLUX_PRO_USERNAME and FLUX_PRO_PASSWORD in .env
```

## 4 Verification

Run these commands to verify installation:

``` bash
# Check PHP version
php -v  # Should be 8.4.12+

# Check Laravel
php artisan --version  # Should be Laravel Framework 12.x

# Check Bun
bun --version  # Should be 1.1.0+

# Run tests
php artisan test

# Check database
php artisan migrate:status
```

## 5 Next Steps

For detailed setup instructions, see:

- [README.md](README.md) - Main documentation index
- [php-runtime.md](030-php-runtime.md) - PHP setup details
- [laravel-core.md](040-laravel-core.md) - Laravel configuration
- [frontend-build.md](130-frontend-build.md) - Frontend setup

## 6 Navigation

[← Setup Documentation](README.md) | [↑ Top](#quick-start-guide) | [Project Overview and Architecture →](020-overview.md)
