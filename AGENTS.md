<!-- trunk-ignore-all(markdownlint/MD033) -->
<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

<details>
<summary>Expand for Table of Contents</summary>

- [Laravel Boost Guidelines](#laravel-boost-guidelines)
  - [1. Foundational Context](#1-foundational-context)
  - [2. Conventions](#2-conventions)
    - [2.1. Naming Conventions](#21-naming-conventions)
  - [3. Verification Scripts](#3-verification-scripts)
    - [3.1. Build/Lint/Test Commands](#31-buildlinttest-commands)
  - [4. Application Structure \& Architecture](#4-application-structure--architecture)
    - [4.1. Architecture Patterns](#41-architecture-patterns)
  - [5. Frontend Bundling](#5-frontend-bundling)
  - [6. Replies](#6-replies)
  - [7. Documentation Files](#7-documentation-files)
  - [8. Laravel Boost](#8-laravel-boost)
  - [9. Artisan](#9-artisan)
  - [10. URLs](#10-urls)
  - [11. Tinker / Debugging](#11-tinker--debugging)
  - [12. Reading Browser Logs With the `browser-logs` Tool](#12-reading-browser-logs-with-the-browser-logs-tool)
  - [13. Searching Documentation (Critically Important)](#13-searching-documentation-critically-important)
    - [13.1. Available Search Syntax](#131-available-search-syntax)
  - [14. PHP](#14-php)
    - [14.1. Constructors](#141-constructors)
    - [14.2. Type Declarations](#142-type-declarations)
    - [14.3. Import Organization](#143-import-organization)
    - [14.4. Error Handling](#144-error-handling)
  - [15. Comments](#15-comments)
  - [16. PHPDoc Blocks](#16-phpdoc-blocks)
  - [17. Enums](#17-enums)
  - [18. Do Things the Laravel Way](#18-do-things-the-laravel-way)
    - [18.1. Database](#181-database)
    - [18.2. Model Creation](#182-model-creation)
    - [18.3. APIs \& Eloquent Resources](#183-apis--eloquent-resources)
    - [18.4. 18.4.Controllers \& Validation](#184-184controllers--validation)
    - [18.5. Queues](#185-queues)
    - [18.6. Authentication \& Authorization](#186-authentication--authorization)
    - [18.7. URL Generation](#187-url-generation)
    - [18.8. 18.8.Configuration](#188-188configuration)
    - [18.9. 18.9.Testing](#189-189testing)
    - [18.10. 18.10 Vite Error](#1810-1810-vite-error)
  - [19. Laravel 12](#19-laravel-12)
    - [19.1. Laravel 12 Structure](#191-laravel-12-structure)
    - [19.2. Database](#192-database)
    - [19.3. Models](#193-models)
  - [20. Laravel Pint Code Formatter](#20-laravel-pint-code-formatter)
  - [21. Pest](#21-pest)
    - [21.1. Testing](#211-testing)
    - [21.2. Pest Tests](#212-pest-tests)
    - [21.3. Running Tests](#213-running-tests)
    - [21.4. Pest Assertions](#214-pest-assertions)
    - [21.5. 21.5.Mocking](#215-215mocking)
    - [21.6. Datasets](#216-datasets)
  - [22. Pest 4](#22-pest-4)
    - [22.1. Browser Testing](#221-browser-testing)
    - [22.2. 22.2 Example Tests](#222-222-example-tests)
  - [23. Tailwind Core](#23-tailwind-core)
    - [23.1. Spacing](#231-spacing)
    - [23.2. Dark Mode](#232-dark-mode)
  - [24. Tailwind 4](#24-tailwind-4)
    - [24.1. Replaced Utilities](#241-replaced-utilities)
  - [25. Test Enforcement](#25-test-enforcement)
  - [27. Security](#27-security)
    - [27.1. Core Security Principles](#271-core-security-principles)
    - [27.2. Security Standards Reference](#272-security-standards-reference)
  - [28. Performance Optimization](#28-performance-optimization)
    - [28.1. Database Performance](#281-database-performance)
    - [28.2. Caching Strategies](#282-caching-strategies)
    - [28.3. Frontend Performance](#283-frontend-performance)
    - [28.4. Application Performance](#284-application-performance)
  - [29. AI-GUIDELINES Integration](#29-ai-guidelines-integration)
    - [29.1. Key Documentation References](#291-key-documentation-references)
    - [29.2. Decision-Making Protocol](#292-decision-making-protocol)
    - [29.3. Sensitive Actions Rule Citation](#293-sensitive-actions-rule-citation)
  - [1. `byterover-store-knowledge`](#1-byterover-store-knowledge)
  - [2. `byterover-retrieve-knowledge`](#2-byterover-retrieve-knowledge)

</details>

## 1. Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.12
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- larastan/larastan (LARASTAN) - v3
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4

> **Note:** This document focuses on Laravel Boost-specific guidelines. For comprehensive development standards, security requirements, performance optimization, and detailed testing standards, refer to [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) and the [AI-GUIDELINES/](.ai/AI-GUIDELINES/) directory.

## 2. Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

### 2.1. Naming Conventions

- **Classes**: PascalCase (e.g., `LinkValidator`, `UserController`)
- **Methods**: camelCase (e.g., `validateLink()`, `getUserData()`)
- **Variables**: camelCase with descriptive names (e.g., `isRegisteredForDiscounts`, not `discount()`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_RETRY_ATTEMPTS`)
- **Files**: PascalCase for classes matching the class name, kebab-case for configs and non-class files

## 3. Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

### 3.1. Build/Lint/Test Commands

- **Build**: `composer install --optimize-autoloader`
- **Lint**: `composer cs-check` (dry-run) or `composer cs-fix` (apply fixes), or use `vendor/bin/pint --dirty` (see section 20)
- **Static Analysis**: `composer analyse` (PHPStan level **max** / **10**) - ensure Larastan is configured for maximum strictness compliance
  - **⚠️ Inconsistency Note:** AGENTS.md previously stated Level 8, but `phpstan.neon` is configured with `level: max` (which is Level 10), and [AI-GUIDELINES](.ai/AI-GUIDELINES/PHP-Laravel/020-development-standards.md#611-phpstan-level-10-max-requirements) specifies Level 10. The project standard is **Level 10/max**.
- **Test All**: `composer test` (Pest framework) or `php artisan test`
- **Test Single**: `composer test:filter "test_name"` or `php artisan test --filter=testName` or `pest --filter test_name`
- **Test by Type**: `composer test:unit`, `composer test:integration`, `composer test:configuration`, `composer test:performance` (if available)
- **Coverage**: `composer test:coverage` (if available)
- **Quality**: `composer quality` (runs cs-check, analyse, test) - if available

## 4. Application Structure & Architecture

- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

### 4.1. Architecture Patterns

For comprehensive architecture guidance, refer to [AI-GUIDELINES/PHP-Laravel/020-development-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/020-development-standards.md#3-architecture-patterns). Key patterns include:

- **Domain-Driven Design**: Clear layer separation (Application, Domain, Infrastructure, Presentation)
- **State Management**: Use `spatie/laravel-model-states` and `spatie/laravel-model-status` for state machines
- **Feature Flags**: Use `spatie/laravel-model-flags` for feature flags backed by flags enum
- **UI Components**: Implement Livewire UI components as Volt Single File Components (SFC)
- **Custom Blade Directives**: Include suitable prefixes in names for uniqueness

## 5. Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## 6. Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## 7. Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## 8. Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## 9. Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## 10. URLs

- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## 11. Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## 12. Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## 13. Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### 13.1. Available Search Syntax

- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - `queries=["authentication", "middleware"]` - ANY of these terms

=== php rules ===

## 14. PHP

- Always use curly braces for control structures, even if it has one line.
- **PSR-12** coding standards must be followed.
- **Strict types**: All PHP files must start with `<?php\ndeclare(strict_types=1);`
- **PHP 8.1+** features with type declarations (this project uses PHP 8.4.12)
- **PHPStan Level 10 (max)** compliance required - Larastan v3 is configured for maximum strictness
  - **⚠️ Inconsistency Note:** Updated from Level 8 to Level 10/max to match `phpstan.neon` configuration and [AI-GUIDELINES](.ai/AI-GUIDELINES/PHP-Laravel/020-development-standards.md#611-phpstan-level-10-max-requirements) standards.

### 14.1. Constructors

- Use PHP 8 constructor property promotion in `__construct()`.

```php
public function __construct(public GitHub $github) { }
```

- Do not allow empty `__construct()` methods with zero parameters.

### 14.2. Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.
- Type-safe code is required - PHPStan Level 8 compliance ensures this.

```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

### 14.3. Import Organization

- Alphabetical order within groups
- Group imports: 1) PHP built-ins, 2) external libraries/vendor packages, 3) internal application classes
- Use fully qualified names only when necessary to avoid ambiguity

### 14.4. Error Handling

- Use typed exceptions for specific error cases
- Implement proper logging with context
- Never expose sensitive information in error messages
- Use early returns to reduce nesting and improve readability

## 15. Comments

- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## 16. PHPDoc Blocks

- Add useful array shape type definitions for arrays when appropriate.
- PHPDoc blocks are required for all public methods and properties.
- **Prefer PHP 8 attributes over PHPDocs** for robust type safety:
  - Use attributes for route definitions (`#[Route]`)
  - Use attributes for validation rules (`#[Rule]`)
  - Use attributes for middleware (`#[Middleware]`)
  - Use attributes for dependency injection (`#[Inject]`)
  - Use attributes for event listeners (`#[ListensTo]`)
  - Use attributes for policies (`#[Policy]`)
  - See [AI-GUIDELINES/PHP-Laravel/020-development-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/020-development-standards.md#52-modern-php-and-laravel-features) for examples
- Include `@throws` for all exceptions that may be thrown by a method.
- Document complex algorithms with inline comments when necessary (though prefer PHPDoc blocks).

## 17. Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== laravel/core rules ===

## 18. Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### 18.1. Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### 18.2. Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### 18.3. APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### 18.4. 18.4.Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### 18.5. Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### 18.6. Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### 18.7. URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

### 18.8. 18.8.Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### 18.9. 18.9.Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### 18.10. 18.10 Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

## 19. Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### 19.1. Laravel 12 Structure

- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### 19.2. Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### 19.3. Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

## 20. Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.
- Alternatively, if composer scripts are available, use `composer cs-fix` to apply fixes or `composer cs-check` for a dry-run.

=== pest/core rules ===

## 21. Pest

### 21.1. Testing

- If you need to verify a feature is working, write or update a Unit / Feature test.

### 21.2. Pest Tests

- All tests must be written using Pest. Use `php artisan make:test --pest <name>`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:

```php
it('is true', function () {
    expect(true)->toBeTrue();
});
```

### 21.3. Running Tests

- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### 21.4. Pest Assertions

- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:

```php
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
```

### 21.5. 21.5.Mocking

- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### 21.6. Datasets

- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

```php
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
```

=== pest/v4 rules ===

## 22. Pest 4

- Pest v4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### 22.1. Browser Testing

- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest v4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### 22.2. 22.2 Example Tests

```php
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
```

```php
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
```

=== tailwindcss/core rules ===

## 23. Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### 23.1. Spacing

- When listing items, use gap utilities for spacing, don't use margins.

```html
    <div class="flex gap-8">
        <div>Superior</div>
        <div>Michigan</div>
        <div>Erie</div>
    </div>
```

### 23.2. Dark Mode

- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

=== tailwindcss/v4 rules ===

## 24. Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

```diff
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
```

### 24.1. Replaced Utilities

- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated            | Replacement          |
|-----------------------|----------------------|
| bg-opacity-*          | bg-black/*           |
| text-opacity-*        | text-black/*         |
| border-opacity-*.     | border-black/*       |
| divide-opacity-*      | divide-black/*       |
| ring-opacity-*        | ring-black/*         |
| placeholder-opacity-* | placeholder-black/*  |
| flex-shrink-*.        | shrink-*             |
| flex-grow-*.          | grow-*               |
| overflow-ellipsis     | text-ellipsis        |
| decoration-slice      | box-decoration-slice |
| decoration-clone.     | box-decoration-clone |

=== tests rules ===

## 25. Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.
- **90% minimum code coverage required** - aim for comprehensive test coverage.
- **Test Organization**: Tests MUST be organized in a directory structure that mirrors the application's `app/` directory:
  - `tests/Unit/`: For testing individual components (Models, Services, etc.) in isolation
  - `tests/Feature/`: For testing features from a command-line or HTTP perspective
  - `tests/Integration/`: For testing the interaction between multiple components
  - `tests/E2E/`: For end-to-end tests that simulate a complete user scenario
  - `tests/Architecture/`: For enforcing architectural rules using `pest-plugin-arch`
  - `tests/Browser/`: For Pest 4 browser tests
  - `tests/Support/`: Contains test helpers, data builders, and other support classes
- Use database transactions for isolation when testing database operations (`DatabaseTransactions` trait).
- **PHPStan Level 10 compliance in tests**: All test files MUST strive for PHPStan Level 10 compliance. Use typed mocks and explicit type annotations.
- Type-safe test code with PHPStan compliance - tests should follow the same coding standards as application code.
- **Test Categories**: Use `#[Group]` attributes to categorize tests (e.g., `unit`, `feature`, `integration`, `database`, `api`, `security`).
- For comprehensive testing standards, see [030-testing-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/030-testing-standards.md).

## 27. Security

> **📚 Comprehensive Security Standards**: For detailed security requirements, refer to [AI-GUIDELINES/PHP-Laravel/030-security-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/040-security-standards.md) and [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md#5-general-security-principles).

### 27.1. Core Security Principles

- **No Secrets in Repository**: Never commit secrets, API keys, passwords, tokens, or any other bearer credentials to version control.
  - If a scanning tool detects a secret-like token, the task MUST fail, and the secret must be remediated immediately.
- **Input Validation**: Validate all inputs with proper sanitization using Laravel Form Requests with whitelist-based validation.
- **Database Security**: Use parameterized queries via Eloquent ORM - never use raw queries with user input.
- **Authentication & Authorization**: Implement proper authentication and authorization checks using Laravel's built-in features (gates, policies, Sanctum, etc.).
- **Session Security**: In production, session cookies MUST be configured as `SESSION_SECURE=true`, `SESSION_HTTP_ONLY=true`, and `SESSION_SAME_SITE=strict`.

### 27.2. Security Standards Reference

For comprehensive security implementation covering:

- Multi-Factor Authentication (MFA) with Laravel Fortify
- Role-Based Access Control (RBAC) with FilamentShield
- Data encryption at rest and in transit
- XSS, CSRF, and SQL Injection prevention
- API security with Sanctum and rate limiting
- File upload security with malware scanning
- Security event logging and incident response

See: [AI-GUIDELINES/PHP-Laravel/030-security-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/040-security-standards.md)

## 28. Performance Optimization

> **📚 Comprehensive Performance Standards**: For detailed performance optimization strategies, refer to [AI-GUIDELINES/PHP-Laravel/040-performance-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/050-performance-standards.md).

### 28.1. Database Performance

- **Prevent N+1 Queries**: Use eager loading (`with()`) in Eloquent queries - this is a critical performance requirement.
- **Indexing**: Implement proper database indexing strategies for all frequently queried columns.
- **Transaction Management**: Keep database transactions as short as possible to reduce lock times.

### 28.2. Caching Strategies

- **Application-Level Caching**: Use Laravel's cache system (`Cache::remember`) for expensive operations.
- **HTTP Caching**: Implement HTTP response caching for static or semi-static content.

### 28.3. Frontend Performance

- **Asset Optimization**: All JavaScript and CSS files MUST be minified and bundled for production using Vite.
- **Code Splitting**: Implement code splitting for large JavaScript applications.
- **Image Optimization**: Use modern, compressed image formats (e.g., WebP, AVIF) and implement lazy loading.

### 28.4. Application Performance

- **Memory-Efficient Code**: Use PHP generators or Eloquent's `chunk()` or `cursor()` methods for large datasets.
- **Queue for Background Jobs**: Offload time-consuming tasks to queue workers.
- **Performance Monitoring**: Implement Application Performance Monitoring (APM) to track key metrics.

For comprehensive performance standards, see: [050-performance-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/050-performance-standards.md)

## 29. AI-GUIDELINES Integration

This document (AGENTS.md) focuses on Laravel Boost-specific guidelines and workflow patterns. For comprehensive development standards, refer to the [AI-GUIDELINES](.ai/AI-GUIDELINES.md) system:

### 29.1. Key Documentation References

- **[AI-GUIDELINES.md](.ai/AI-GUIDELINES.md)**: Main guidelines document with orchestration policy, security principles, and decision-making protocols
- **[PHP-Laravel Development Standards](.ai/AI-GUIDELINES/PHP-Laravel/020-development-standards.md)**: Complete development standards including:
  - PHPStan Level 10 requirements
  - Modern PHP 8.4 features and Laravel 12 patterns
  - Architecture patterns (Domain-Driven Design, plugin architecture)
  - Code organization and structure
- **[Testing Standards](.ai/AI-GUIDELINES/PHP-Laravel/030-testing-standards.md)**: Comprehensive testing requirements including:
  - Test organization and structure
  - PHPStan Level 10 compliance in tests
  - Test categories and grouping
  - Test data management
- **[Security Standards](.ai/AI-GUIDELINES/PHP-Laravel/040-security-standards.md)**: Complete security implementation guide
- **[Performance Standards](.ai/AI-GUIDELINES/PHP-Laravel/050-performance-standards.md)**: Performance optimization strategies
- **[Documentation Standards](.ai/AI-GUIDELINES/Documentation/010-documentation-standards.md)**: Documentation creation and maintenance standards

### 29.2. Decision-Making Protocol

When making code changes, follow the review steps from [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md#33-decision-making-protocol):

1. **Review Guidelines**: Check [PHP-Laravel/020-development-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/020-development-standards.md) for relevant patterns
2. **Security Assessment**: Apply rules from [040-security-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/040-security-standards.md)
3. **Performance Impact**: Consider implications from [050-performance-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/050-performance-standards.md)
4. **Testing Strategy**: Plan tests according to [030-testing-standards.md](.ai/AI-GUIDELINES/PHP-Laravel/030-testing-standards.md)
5. **Documentation Needs**: Identify any required documentation changes based on [Documentation/010-documentation-standards.md](.ai/AI-GUIDELINES/Documentation/010-documentation-standards.md)

### 29.3. Sensitive Actions Rule Citation

When performing sensitive actions (security-affecting changes, code execution, external access), agents MUST cite the exact rule(s) they are following with a clickable reference to the specific file and line number, per [AI-GUIDELINES.md](.ai/AI-GUIDELINES.md#43-sensitive-actions-rule-citation).

Example: rule [040-security-standards.md#L41](.ai/AI-GUIDELINES/PHP-Laravel/040-security-standards.md#L41)

</laravel-boost-guidelines>

[byterover-mcp]

[byterover-mcp]

You are given two tools from Byterover MCP server, including
## 1. `byterover-store-knowledge`
You `MUST` always use this tool when:

+ Learning new patterns, APIs, or architectural decisions from the codebase
+ Encountering error solutions or debugging techniques
+ Finding reusable code patterns or utility functions
+ Completing any significant task or plan implementation

## 2. `byterover-retrieve-knowledge`
You `MUST` always use this tool when:

+ Starting any new task or implementation to gather relevant context
+ Before making architectural decisions to understand existing patterns
+ When debugging issues to check for previous solutions
+ Working with unfamiliar parts of the codebase
