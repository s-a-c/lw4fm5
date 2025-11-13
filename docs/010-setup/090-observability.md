# Telescope, Activity Log, and Health Checks

Compliant with [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers observability packages: Laravel Telescope, Spatie Activity Log, and Spatie Health.

## 2 Laravel Telescope

### 2.1 Package Overview

**Package Name**: `laravel/telescope`
**Version**: `^5.15`
**Purpose**: Debug and monitor Laravel application
**Architectural Role**: Provides detailed insights into requests, queries, jobs, and more

### 2.2 Key Features

- Request monitoring
- Query logging
- Job tracking
- Mail preview
- Exception tracking
- Cache monitoring
- Model events

**Source**: [Laravel Telescope Documentation](https://laravel.com/docs/12.x/telescope)

### 2.3 Installation Verification

``` bash
composer show laravel/telescope
php artisan telescope:install
```

### 2.4 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Install Telescope | `php artisan telescope:install` | Migrations and assets published | Files created |
| 2 | Run migrations | `php artisan migrate` | Telescope tables created | Tables exist |
| 3 | Configure authorization | Edit `app/Providers/TelescopeServiceProvider.php` | Access control configured | Provider updated |
| 4 | Access dashboard | Visit `/telescope` in browser | Dashboard accessible | Login required |

## 3 Spatie Activity Log

### 3.1 Package Overview

**Package Name**: `spatie/laravel-activitylog`
**Version**: `^4.10`
**Purpose**: Log activity on Eloquent models
**Architectural Role**: Tracks changes to models for audit trails

### 3.2 Installation Verification

``` bash
composer show spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
```

## 4 Spatie Health

### 4.1 Package Overview

**Package Name**: `spatie/laravel-health`
**Version**: `^1.34`
**Purpose**: Health check endpoints for application monitoring
**Architectural Role**: Provides health status for infrastructure monitoring

### 4.2 Installation Verification

``` bash
composer show spatie/laravel-health
```

## 5 Spatie Schedule Monitor

### 5.1 Package Overview

**Package Name**: `spatie/laravel-schedule-monitor`
**Version**: `^4.1`
**Purpose**: Monitor Laravel scheduled tasks
**Architectural Role**: Tracks scheduled task execution and failures

### 5.2 Installation Verification

``` bash
composer show spatie/laravel-schedule-monitor
```

## 6 Environment Variables

No specific environment variables required.

## 7 Next Steps

[Spatie Packages →](100-spatie-packages.md)

## 8 Navigation

[← Horizon and Octane Queue Management](080-queue-monitoring.md) | [↑ Top](#telescope-activity-log-and-health-checks) | [Spatie Laravel Packages Configuration →](100-spatie-packages.md)
