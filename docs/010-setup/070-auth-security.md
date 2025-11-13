# Fortify and WorkOS Authentication Setup

Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
<!-- markdownlint-disable MD013 -->

## 1 Introduction

This document covers the installation and configuration of Laravel Fortify and Laravel WorkOS for authentication and user management.

## 2 Laravel Fortify

### 2.1 Package Overview

**Package Name**: `laravel/fortify`
**Version**: `^1.31`
**Purpose**: Backend authentication implementation for Laravel
**Architectural Role**: Provides authentication features without UI components

**What is Laravel Fortify?**
Laravel Fortify is a frontend-agnostic authentication package that provides all the backend authentication logic for your Laravel application. It handles user registration, login, password resets, email verification, and two-factor authentication - but doesn’t provide any UI components.

**Why use Fortify?**
\* **Backend Only**: Provides authentication logic without forcing a specific UI framework
\* **Flexible UI**: Use any frontend (Blade, Livewire, Inertia, Vue, React, etc.)
\* **Feature Flags**: Enable or disable features via configuration
\* **Event-Driven**: Uses Laravel events, making it easy to customize behavior
\* **Complete Authentication**: Includes registration, login, 2FA, password reset, and more

**How Fortify Works:**

1. Fortify provides routes and controllers for authentication actions
2. You build your own UI (forms, buttons, etc.) that submit to Fortify’s routes
3. Fortify handles the authentication logic (validation, hashing, sessions, etc.)
4. You can customize behavior by listening to Fortify events

**Real-World Examples:**
\* **Registration Form**: User fills out a form, submits to Fortify, account is created
\* **Login Form**: User enters email/password, Fortify authenticates and creates session
\* **Password Reset**: User requests reset, Fortify sends email, user resets password
\* **2FA Setup**: User enables 2FA, Fortify generates QR code, user scans with authenticator app

### 2.2 Key Features

- **User Registration**: Complete registration system with validation
- **Login/Logout**: Secure authentication and session management
- **Password Reset**: Email-based password reset functionality
- **Email Verification**: Verify user email addresses before allowing access
- **Two-Factor Authentication (2FA)**: Add an extra layer of security with TOTP
- **Password Confirmation**: Require password confirmation for sensitive actions
- **Account Deletion**: Allow users to delete their accounts
- **Browser Session Management**: View and manage active sessions
- **Recovery Codes**: Backup codes for 2FA recovery
- **Remember Me**: Option to stay logged in

### 2.3 Principles and Patterns

- **Backend-Only Pattern**: Authentication logic without UI
- **Feature Flags**: Enable/disable features via configuration
- **Event-Driven**: Uses Laravel events for extensibility

**Source**: [Laravel Fortify Documentation](https://laravel.com/docs/12.x/fortify)

### 2.4 Installation Using Artisan Command

Fortify is typically installed using the `php artisan fortify:install` command, which sets up routes, configuration, and migrations.

**Installation Command:**

``` bash
php artisan fortify:install
```

**What Happens During Installation:**

1. **Publishes Configuration**: Creates `config/fortify.php`
2. **Publishes Migrations**: Creates migration for two-factor authentication tables
3. **Registers Service Provider**: Automatically registered in `bootstrap/providers.php`
4. **Creates Actions**: Creates action classes for customization (optional)

**Expected Output:**

``` text
  INFO  Publishing Fortify configuration file...

  INFO  Publishing Fortify migrations...

  INFO  Fortify installed successfully.
```

### 2.5 Installation Verification

``` bash
# Verify Fortify installation
composer show laravel/fortify

# Expected output shows version ^1.31
```

### 2.6 What Gets Installed

#### 2.6.1 Files Created

- `config/fortify.php` - Fortify configuration file
- `database/migrations/YYYY_MM_DD_HHMMSS_create_two_factor_authentications_table.php` - 2FA migration (if enabled)
- `app/Actions/Fortify/` - Action classes for customization (optional)

#### 2.6.2 Configuration Updates

- `bootstrap/providers.php` - FortifyServiceProvider automatically registered
- Routes automatically registered (login, register, logout, etc.)

#### 2.6.3 Database Requirements

- Standard Laravel `users` table (from default Laravel installation)
- `two_factor_authentications` table (if 2FA is enabled)

### 2.7 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify installation | `composer show laravel/fortify` | Package version displayed | ^1.31 shown |
| 2 | Install Fortify | `php artisan fortify:install` | Configuration and migrations published | `config/fortify.php` exists |
| 3 | Run migrations | `php artisan migrate` | Two-factor authentication table created (if enabled) | Tables exist in database |
| 4 | Configure features | Edit `config/fortify.php` → `features` array | Features enabled/disabled | Config file updated |
| 5 | Verify service provider | Check `bootstrap/providers.php` for FortifyServiceProvider | Provider registered | Provider listed |
| 6 | Create authentication views (if using Blade) | Create login/register views that submit to Fortify routes | Authentication forms created | Forms accessible in browser |

### 2.8 Feature Configuration

Fortify uses a feature-based system. You enable or disable features by adding them to the `features` array in `config/fortify.php`.

**Default Configuration:**

``` php
use Laravel\Fortify\Features;

'features' => [// User registration (sign up)
    Features::registration(),

    // Password reset via email
    Features::resetPasswords(),

    // Email verification (users must verify email)
    Features::emailVerification(),

    // Update profile information
    Features::updateProfileInformation(),

    // Update passwords
    Features::updatePasswords(),

    // Two-factor authentication
    Features::twoFactorAuthentication(['confirmPassword' => true,  // Require password confirmation before enabling 2FA
    ]),
],
```

**Understanding Each Feature:**

- **`registration()`**: Allows users to create new accounts
- **`resetPasswords()`**: Allows users to reset forgotten passwords via email
- **`emailVerification()`**: Requires users to verify their email before accessing the application
- **`updateProfileInformation()`**: Allows users to update their name, email, etc.
- **`updatePasswords()`**: Allows users to change their password
- **`twoFactorAuthentication()`**: Enables two-factor authentication for added security

**Customizing Features:**

You can enable or disable features based on your needs. For example, if you don’t want user registration:

``` php
'features' => [// Features::registration(),  // Disabled - no user registration
    Features::resetPasswords(),
    Features::emailVerification(),
    // ... other features
],
```

## 3 Laravel WorkOS

### 3.1 Package Overview

**Package Name**: `laravel/workos`
**Version**: `^0.5`
**Purpose**: Enterprise authentication and user management via WorkOS
**Architectural Role**: Provides SSO, directory sync, and enterprise authentication features

**What is WorkOS?**
WorkOS is a service that provides enterprise authentication features like Single Sign-On (SSO), directory sync, and user management. The Laravel WorkOS package integrates WorkOS into your Laravel application, allowing you to offer enterprise-grade authentication to your users.

**Why use WorkOS?**
\* **Enterprise Features**: SSO, directory sync, and organization management
\* **SAML/OIDC Support**: Integrate with enterprise identity providers
\* **Directory Sync**: Sync users from Active Directory, LDAP, etc.
\* **Multi-tenant**: Built-in support for organizations and teams
\* **Security**: Enterprise-grade security features
\* **Compliance**: Helps meet enterprise security requirements

**When to use WorkOS:**
\* **B2B Applications**: Applications targeting businesses
\* **Enterprise Customers**: Need SSO and directory integration
\* **Multi-tenant SaaS**: Applications with organizations/teams
\* **Compliance Requirements**: Need enterprise authentication features

**How WorkOS Works:**

1. Enterprise customers configure their identity provider (Active Directory, Okta, etc.)
2. Users click "Sign in with SSO" in your application
3. WorkOS handles the authentication flow with the identity provider
4. WorkOS sends the authenticated user back to your application
5. Your application creates/logs in the user

### 3.2 Key Features

- **Single Sign-On (SSO)**: Users sign in once and access multiple applications
- **Directory Sync**: Sync users and groups from enterprise directories
- **User Management**: Manage enterprise users and their access
- **Organization Management**: Manage organizations, teams, and memberships
- **Multi-factor Authentication**: Additional security layer
- **SAML Support**: Industry-standard SSO protocol
- **OIDC Support**: Modern authentication protocol
- **SCIM Provisioning**: Automatically provision users from directories

**Source**: [WorkOS Documentation](https://docs.workos.com) \| [Laravel WorkOS Package](https://github.com/laravel/workos)

### 3.3 Installation Verification

``` bash
# Verify WorkOS installation
composer show laravel/workos

# Check WorkOS configuration
php artisan vendor:publish --tag=workos-config
```

### 3.4 Getting Started with WorkOS

#### 3.4.1 Create WorkOS Account

Before using WorkOS, you need to:

1. **Sign up**: Create an account at <https://workos.com>
2. **Create API Key**: Get your API key from the WorkOS dashboard
3. **Create Client**: Create a client application in WorkOS
4. **Configure SSO**: Set up SSO connections (if needed)

#### 3.4.2 Installation

WorkOS is typically already installed via Composer. Verify installation:

``` bash
composer show laravel/workos
```

### 3.5 Configuration Steps

| Step \# | Task/Configuration Item | Command/File/Code | Expected Result | Verification Method |
|----|----|----|----|----|
| 1 | Verify installation | `composer show laravel/workos` | Package version displayed | ^0.5 shown |
| 2 | Create WorkOS account | Sign up at <https://workos.com> | Account created | Can access WorkOS dashboard |
| 3 | Get WorkOS API key | Copy API key from WorkOS dashboard | API key obtained | Key available to copy |
| 4 | Get WorkOS Client ID | Copy Client ID from WorkOS dashboard | Client ID obtained | ID available |
| 5 | Configure environment variables | Set `WORKOS_API_KEY` and `WORKOS_CLIENT_ID` in `.env` | Environment variables set | Check `.env` file |
| 6 | Publish configuration (optional) | `php artisan vendor:publish --tag=workos-config` | Config file created | `config/workos.php` exists (if published) |
| 7 | Configure WorkOS in application | Use WorkOS in your authentication flow | WorkOS integrated | SSO/login working |

**Note**: WorkOS configuration is typically done in your authentication controllers or service providers, not always in a config file. The package provides helper methods to interact with WorkOS.

## 4 Environment Variables

| Variable           | Description                             | Default    |
|--------------------|-----------------------------------------|------------|
| WORKOS_API_KEY     | WorkOS API key                          | (required) |
| WORKOS_CLIENT_ID   | WorkOS client ID                        | (required) |
| WORKOS_ENVIRONMENT | WorkOS environment (production/staging) | production |

## 5 Integration Points

### 5.1 Fortify with Livewire

Fortify works seamlessly with Livewire components for authentication UI.

### 5.2 WorkOS with Fortify

WorkOS can complement Fortify by providing enterprise SSO features.

## 6 Troubleshooting

### 6.1 Registration Not Working

**Symptom**: User registration fails

**Solution**: Verify features are enabled in `config/fortify.php`:

``` php
Features::registration(),
```

### 6.2 WorkOS Connection Failed

**Symptom**: Cannot connect to WorkOS API

**Solution**: Verify API key and client ID in `.env`:

``` bash
php artisan tinker
>>> config('workos.api_key')
```

## 7 Next Steps

After configuring authentication:

- [queue-monitoring.md](080-queue-monitoring.md) - Set up Horizon and Octane

## 8 Navigation

[← Filament 5.x-dev Admin Panel Setup](060-admin-panel.md) | [↑ Top](#fortify-and-workos-authentication-setup) | [Horizon and Octane Queue Management →](080-queue-monitoring.md)
