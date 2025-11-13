# Scout, Typesense, and Analytics Setup

Compliant with [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers Laravel Scout for search, Typesense integration, and Spatie Analytics.

## 2 Laravel Scout

### 2.1 Package Overview

**Package Name**: `laravel/scout`
**Version**: `^10.20`
**Purpose**: Full-text search for Eloquent models
**Architectural Role**: Provides search abstraction layer

### 2.2 Installation Verification

``` bash
composer show laravel/scout
```

**Source**: [Laravel Scout Documentation](https://laravel.com/docs/12.x/scout)

## 3 Typesense

### 3.1 Package Overview

**Package Name**: `typesense/typesense-php`
**Version**: `^5.2.0-RC4`
**Purpose**: Typesense search engine client
**Architectural Role**: Search engine backend for Scout

**Source**: [Typesense Documentation](https://typesense.org/docs) \| [Typesense PHP Client](https://github.com/typesense/typesense-php)

### 3.2 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify installation | `composer show typesense/typesense-php` | Package version displayed | ^5.2.0-RC4 shown |
| 2 | Install Typesense server | Follow Typesense installation guide | Typesense server running | Server responds |
| 3 | Configure environment | Set `TYPESENSE_*` variables in `.env` | Environment configured | Variables set |
| 4 | Configure Scout | Edit `config/scout.php` → set driver to typesense | Scout configured | Config updated |

## 4 Environment Variables

| Variable           | Description                      | Default    |
|--------------------|----------------------------------|------------|
| TYPESENSE_HOST     | Typesense server host            | localhost  |
| TYPESENSE_PORT     | Typesense server port            | 8108       |
| TYPESENSE_PROTOCOL | Connection protocol (http/https) | http       |
| TYPESENSE_API_KEY  | Typesense API key                | (required) |

## 5 Next Steps

[Development Tools →](120-development-tools.md)

## 6 Navigation

[← Spatie Laravel Packages Configuration](100-spatie-packages.md) | [↑ Top](#scout-typesense-and-analytics-setup) | [Development Dependencies Setup →](120-development-tools.md)
