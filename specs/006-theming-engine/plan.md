# Implementation Plan: Theming Engine

**Branch**: `006-theming-engine` | **Date**: 2025-11-25 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/006-theming-engine/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

-----

<details>
<summary>Expand for Table of Contents</summary>

- [Implementation Plan: Theming Engine](#implementation-plan-theming-engine)
  - [1. Summary](#1-summary)
  - [2. Technical Context](#2-technical-context)
  - [3. Constitution Check](#3-constitution-check)
    - [3.1. Pre-Research Gates ✅](#31-pre-research-gates-)
    - [3.2. Post-Design Re-Evaluation ✅](#32-post-design-re-evaluation-)
  - [4. Project Structure](#4-project-structure)
    - [4.1. Documentation (this feature)](#41-documentation-this-feature)
    - [4.2. Source Code (repository root)](#42-source-code-repository-root)
  - [5. Implementation Clarifications](#5-implementation-clarifications)
    - [5.1. Default Theme Values](#51-default-theme-values)
    - [5.2. Theme Preview Page Route Specification](#52-theme-preview-page-route-specification)
    - [5.3. Shared Asset Strategy](#53-shared-asset-strategy)
    - [5.4. Telemetry \& Monitoring](#54-telemetry--monitoring)
    - [5.5. Public Pages Scope](#55-public-pages-scope)
    - [5.6. CSS Implementation Details](#56-css-implementation-details)
    - [5.7. Validation & Error Handling](#57-validation--error-handling)
    - [5.8. Performance Targets](#58-performance-targets)
    - [5.9. Testing Requirements](#59-testing-requirements)
  - [6. Analysis Findings Integration](#6-analysis-findings-integration)
    - [6.1. Resolved Issues](#61-resolved-issues)
    - [6.2. Code Organization Improvements](#62-code-organization-improvements)
    - [6.3. Implementation Notes](#63-implementation-notes)
  - [7. "MUST DEFINE" Requirements - Concrete Definitions](#7-must-define-requirements---concrete-definitions)
    - [7.1. Data Model & Integrity Definitions](#71-data-model--integrity-definitions)
    - [7.2. Performance Definitions](#72-performance-definitions)
    - [7.3. Observability Definitions](#73-observability-definitions)
    - [7.4. UX Definitions](#74-ux-definitions)
    - [7.5. Security Definitions](#75-security-definitions)
  - [8. Complexity Tracking](#8-complexity-tracking)

</details>

-----

## 1. Summary

Complete the implementation of a Theming Engine that allows users to customize the visual appearance of the application (Theme, Flavor, Accent) with immediate live preview and auto-save. The system uses a hybrid approach: server-side injection of data attributes for initial page load via View Composer, plus client-side JavaScript updates for instant preview via Livewire. Themes apply globally across all pages including Filament admin panels and authentication pages. Invalid theme combinations are silently auto-corrected on every access. A public theme preview page (`/themes/preview`) allows unauthenticated visitors to preview all themes with temporary session storage.

**Technical Approach**:

- Server-side: View Composer in `AppServiceProvider` injects theme data into all views
- Client-side: Livewire component updates DOM attributes via `$this->js()` for <200ms live preview
- CSS: Attribute selectors (`[data-theme="catppuccin"][data-flavor="mocha"]`) apply theme colors
- Validation: Theme/flavor/accent combinations validated on every access (whenever settings are read), auto-corrected if invalid
- Theme Preview Page: Session storage for temporary theme changes, resets on navigation away

-----

## 2. Technical Context

**Language/Version**: PHP 8.4.15
**Primary Dependencies**: Laravel 12, Livewire 4, Livewire Volt 1, Livewire Flux 2 (Pro), Filament 5, Tailwind CSS 3, Spatie Laravel Data 4
**Storage**: MySQL/PostgreSQL (via Laravel Eloquent), JSON column `users.settings` storing `UserSettingsData` DTO
**Testing**: Pest 4 (PHP), PHPUnit 12, Pest Browser Testing
**Target Platform**: Web application (Laravel), server-side rendering with Livewire for reactivity
**Project Type**: Web application (Laravel monolith with Livewire frontend)
**Performance Goals**: Theme changes visible in <200ms after user selection (SC-002). Target: p95 latency < 200ms for client-side DOM updates.
**Constraints**:

- Must integrate with Livewire Flux color system (zinc palette)
- Must integrate with Filament color system (gray palette mapped to zinc)
- Must support server-side initial load (no FOUC - Flash of Unstyled Content)
- Must support client-side live updates without page reload
- CSS must use attribute selectors: `[data-theme="catppuccin"][data-flavor="mocha"]` (not CSS classes or dynamically injected variables)
- Invalid theme combinations must be silently auto-corrected on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.)
- Default theme values: `Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary` (explicit defaults, no ambiguity)
**Scale/Scope**:
- 2 themes (Catppuccin, Kanagawa)
- 7 flavors total (4 Catppuccin: Latte, Frappe, Macchiato, Mocha; 3 Kanagawa: Wave, Dragon, Lotus)
- 4 accent colors (Primary, Blue, Red, Green)
- Global application across all pages:
  - Main application pages (all Folio pages)
  - Filament admin panels (all Filament panel views)
  - Authentication pages (all Fortify auth views: login, register, password reset, etc.)
  - Public pages (all Folio pages accessible without authentication)

-----

## 3. Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

**Note**: The constitution file (`.specify/memory/constitution.md`) appears to be a template. No specific constitution gates are defined. Proceeding with standard Laravel/Livewire best practices:

### 3.1. Pre-Research Gates ✅

- ✅ **Test-First**: All changes must be tested (Pest 4, 90%+ coverage minimum)
- ✅ **Laravel Conventions**: Use Laravel's built-in features (View Composers, Eloquent casts, Service Providers)
- ✅ **Livewire Best Practices**: Follow Livewire Volt patterns, use wire:model.live for reactivity
- ✅ **Security**: Validate theme/flavor/accent combinations on every access, prevent invalid data persistence, handle validation errors (prevent save, log error, notify user)
- ✅ **Performance**: Theme changes must be <200ms (client-side updates), server-side injection prevents FOUC

### 3.2. Post-Design Re-Evaluation ✅

After Phase 1 design, all gates remain satisfied:

- ✅ **Test-First**: Comprehensive test strategy defined (unit, feature, browser tests)
- ✅ **Laravel Conventions**:
  - View Composer pattern for global theme injection
  - Eloquent casts for UserSettingsData
  - Service Provider for theme service (optional)
- ✅ **Livewire Best Practices**:
  - Existing component already uses `wire:model.live`
  - `$this->js()` for instant DOM updates
  - Auto-save pattern implemented
- ✅ **Security**:
  - Validation logic defined in data-model.md
  - Invalid combinations auto-corrected silently on every access
  - Validation errors during save: prevent save, log error, notify user
  - No cross-user access (user-specific settings)
- ✅ **Performance**:
  - Server-side injection prevents FOUC
  - Client-side updates <200ms via direct DOM manipulation
  - CSS attribute selectors use native browser performance

**No violations detected. All gates pass.**

-----

## 4. Project Structure

### 4.1. Documentation (this feature)

```log
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### 4.2. Source Code (repository root)

```text
app/
├── Data/
│   ├── UserSettingsData.php          # Existing: Spatie Data DTO for theme settings
│   └── ThemeData.php                 # NEW: Spatie Data DTO for View Composer theme injection
├── Enums/
│   ├── Theme.php                     # Existing: Theme enum (Catppuccin, Kanagawa)
│   ├── ThemeFlavor.php               # Existing: Flavor enum (Latte, Frappe, etc.)
│   └── ThemeAccent.php               # Existing: Accent enum (Primary, Blue, etc.)
├── Models/
│   └── User.php                      # Existing: User model with settings cast
├── Providers/
│   └── AppServiceProvider.php        # Modify: Add server-side theme injection via View Composer
└── Services/
    └── Theme/
        ├── ThemeService.php          # NEW: Theme validation (on every access) and default resolution
        └── ThemeResolver.php        # NEW: Theme resolution logic (optional, if service grows)

resources/
├── css/
│   ├── app.css                       # Existing: Main CSS with theme attribute selectors
│   ├── catppuccin.css                # Existing: Catppuccin theme extras
│   └── kanagawa.css                  # Existing: Kanagawa theme extras
├── js/
│   └── app.js                        # Modify: Enhance client-side theme initialization
└── views/
    ├── livewire/
    │   └── settings/
    │       └── appearance.blade.php  # Existing: Appearance settings UI (already implements live preview)
    └── pages/
        └── themes/
            └── preview.blade.php      # NEW: Theme preview page (moved from tailwindcss.catppuccin.com/index.blade.php)

tests/
├── Feature/
│   ├── Theme/
│   │   ├── ThemePersistenceTest.php  # NEW: Test theme persistence and retrieval
│   │   ├── ThemeValidationTest.php   # NEW: Test invalid theme combination handling
│   │   ├── ThemeGlobalApplicationTest.php  # NEW: Test theme applies globally (includes Filament panels and auth pages)
│   │   └── ThemePerformanceTest.php  # NEW: Test p95 latency < 200ms for theme changes (optional)
│   └── ThemePreview/
│       └── ThemePreviewPageTest.php  # NEW: Test public theme preview page functionality
└── Unit/
    └── Services/
        └── Theme/
            └── ThemeServiceTest.php   # NEW: Test theme validation logic

```

**Structure Decision**: Laravel monolith structure. Theming code organized for logical consistency and maintainability:

- **Data Objects**: `app/Data/` - All theme-related DTOs (UserSettingsData, ThemeData)
- **Enums**: `app/Enums/` - All theme-related enums (Theme, ThemeFlavor, ThemeAccent)
- **Services**: `app/Services/Theme/` - Theme-related business logic (validation, resolution)
- **Tests**: Organized to match source structure (Feature/Theme, Feature/ThemePreview, Unit/Services/Theme)

**Code Organization Principles**:

- Prefer ENUMs and Data objects over arrays for type safety and IDE support
- Group related functionality (Theme services in `app/Services/Theme/` subdirectory)
- Tests mirror source structure for easy navigation
- All theme-related code follows consistent naming and organization patterns

Tests organized by feature area (Theme, ThemePreview) and unit tests for services. All code uses ENUMs and Data objects instead of arrays where possible.

-----

## 5. Implementation Clarifications

### 5.1. Default Theme Values

**Explicit Defaults** (resolves ambiguity D1):

- Theme: `Theme::Catppuccin` (enum value: `'catppuccin'`)
- Flavor: `ThemeFlavor::Mocha` (enum value: `'mocha'`)
- Accent: `ThemeAccent::Primary` (enum value: `'primary'`)

These defaults apply when:

- New user with no saved settings
- User settings are null
- Invalid theme/flavor/accent combination detected (auto-correction)

### 5.2. Theme Preview Page Route Specification

**Route Details** (resolves underspecification E2, E3):

- **File Path**: `resources/views/pages/themes/preview.blade.php` (renamed from `tailwindcss.catppuccin.com/index.blade.php`)
- **Route**: Folio page route (auto-generated from file path)
- **URL Path**: `/themes/preview`
- **Middleware**: None (public access, no authentication required)
- **Access**: Unauthenticated visitors can access without login
- **Rationale**: Generic name supports future theme additions, more maintainable than theme-specific path

### 5.3. Shared Asset Strategy

- Preview page reuses production `app.css` / `app.js` bundles for exact visual parity.
- Preview-specific session storage logic is injected via a small inline script within `resources/views/pages/themes/preview.blade.php`.
- Preview route should set shorter cache headers to prevent experiments from persisting across navigation while keeping CDN behavior safe for the main app.
- Any additional preview-only assets must be feature-flagged or conditionally loaded to avoid bloating the production bundle.

### 5.4. Telemetry & Monitoring

- Laravel **Telescope** must capture theme validation corrections, preview interactions, and performance markers (SC-002).
- Laravel **Horizon** must be configured to surface queue metrics if background jobs are later introduced for theming (e.g., broadcasting).
- Performance instrumentation (e.g., custom events or timing calls) must record p95 latency for theme changes and make the data queryable via Telescope dashboards.
- Implementation tasks are updated (Phase 6) to configure and verify both tools.

### 5.5. Public Pages Scope

**Definition** (resolves underspecification E1):
"Public pages" refers to all Folio pages accessible without authentication. This includes:

- Theme preview page (`/themes/preview`)
- Any other Folio pages in `resources/views/pages/` that don't require authentication
- Pages accessible via public routes (no `auth` middleware)

Theme injection applies to all Folio pages via View Composer pattern (`View::composer('*', ...)`).

### 5.6. CSS Implementation Details

**Clarification** (resolves terminology F2):

CSS uses **attribute selectors only**, not CSS classes or dynamically injected CSS variables. Implementation pattern:

```css
[data-theme="catppuccin"][data-flavor="mocha"] {
  --color-zinc-900: #1e1e2e;
  /* CSS custom properties defined in attribute selector rules */
}
```

CSS custom properties (variables) are defined statically in CSS files, selected by data attributes. No dynamic CSS injection or class toggling required.

### 5.7. Validation & Error Handling

**Validation Timing** (from Session 3 clarifications):

- **Validate on every access**: Theme/flavor/accent combinations MUST be validated whenever settings are read (View Composer, Livewire mount, direct model access, etc.), not just on load. This ensures data integrity and handles edge cases (corruption, enum changes, migrations). The performance impact is minimal since validation is lightweight.

**Validation Error Handling** (from Session 3 clarifications):

- **During save**: If validation fails during save, the system MUST prevent save, log error, and notify user with a user-friendly error message. This provides complete error handling and aligns with FR-044 (user notification on failures).

**Auto-save Feedback** (from Session 3 clarifications):

- **Silent on success**: Auto-save provides no visual feedback when it succeeds. This provides clean UX and avoids notification fatigue. Users can verify persistence by refreshing the page.

**Data Retention Policy** (from Session 3 clarifications):

- **Retain until account deletion**: Theme preferences are deleted when user account is deleted. This is simple and aligns with user data lifecycle management.

### 5.8. Performance Targets

**Performance Consistency** (from Session 3 clarifications):

- **Same target for all conditions**: System MUST maintain the same performance target (p95 < 200ms) for all load conditions (normal load, high load, network latency scenarios). This keeps acceptance criteria simple and consistent. The system should meet the target under normal and high load; if it doesn't, it's a performance issue to address.

### 5.9. Testing Requirements

**TDD Workflow** (resolves E1, I1):

- All features MUST follow Test-Driven Development (TDD) workflow
- Tests MUST be written before implementation
- Tests MUST fail initially (Red phase)
- Implementation makes tests pass (Green phase)
- Refactor as needed (Refactor phase)
- Test coverage MUST be comprehensive (unit, feature, browser tests as appropriate)

**Test Organization** (resolves I2):

- Tests organized to match source code structure:
  - `tests/Feature/Theme/` - Feature tests for theme functionality
  - `tests/Feature/ThemePreview/` - Feature tests for theme preview page
  - `tests/Unit/Services/Theme/` - Unit tests for theme services
  - `tests/Unit/Data/` - Unit tests for theme Data objects (if needed)
  - `tests/Unit/Enums/` - Unit tests for theme enums (if needed)

**Additional Test Coverage** (resolves coverage gaps I1, I2):

1. **Filament Panel Theme Application**:
   - Test: Verify theme data attributes are present in Filament panel views
   - Test: Verify theme colors apply correctly in Filament components
   - Location: `tests/Feature/Theme/ThemeGlobalApplicationTest.php`
   - Acceptance: User Story 1, Acceptance Scenario 5 (already covers this, but add explicit Filament test)

2. **Authentication Page Theme Application**:
   - Test: Verify theme data attributes are present in Fortify auth views (login, register, password reset, etc.)
   - Test: Verify theme colors apply correctly in auth page components
   - Location: `tests/Feature/Theme/ThemeGlobalApplicationTest.php`
   - Acceptance: User Story 1, Acceptance Scenario 5 (already covers this, but add explicit auth page test)

3. **Performance Testing**:
   - Test: Measure p95 latency for theme change DOM updates
   - Target: p95 < 200ms
   - Location: `tests/Feature/Theme/ThemePerformanceTest.php` (optional, or include in existing tests)

4. **Theme Preview Page Testing**:
   - Test: Verify unauthenticated access to `/themes/preview`
   - Test: Verify session storage behavior
   - Test: Verify theme changes reset on navigation
   - Location: `tests/Feature/ThemePreview/ThemePreviewPageTest.php`

-----

## 6. Analysis Findings Integration

**Date**: 2025-11-25
**Analysis**: `/speckit.analyze` findings incorporated (including user requirements)

### 6.1. Resolved Issues

All analysis recommendations and user requirements have been incorporated into this plan:

- ✅ **D1 (Duplication)**: Public pages definition consolidated
- ✅ **D2 (Ambiguity)**: Performance metric clarified as p95 latency < 200ms
- ✅ **E1 (Underspecification)**: Public pages scope defined as all Folio pages accessible without authentication
- ✅ **E2 (Underspecification)**: Theme preview page route specification added
- ✅ **E3 (Underspecification)**: Theme preview page renamed from `/tailwindcss.catppuccin.com` to `/themes/preview` for better maintainability
- ✅ **F1 (Terminology)**: All `isDark` references replaced with `isLight()` method (matches existing code)
- ✅ **F2 (Terminology)**: `themeData` changed from array to `ThemeData` DTO class
- ✅ **F3 (Terminology)**: `availableFlavors` documented as `array<ThemeFlavor>` with PHPDoc
- ✅ **I1 (Coverage Gap)**: TDD workflow explicitly mandated (FR-013 added to spec)
- ✅ **I2 (Coverage Gap)**: Test organization aligned with source code structure

### 6.2. Code Organization Improvements

- **Data Objects**: All theme DTOs in `app/Data/` (UserSettingsData, ThemeData)
- **Enums**: All theme enums in `app/Enums/` (Theme, ThemeFlavor, ThemeAccent)
- **Services**: Theme services organized in `app/Services/Theme/` subdirectory
- **Tests**: Tests organized to match source structure (Feature/Theme, Feature/ThemePreview, Unit/Services/Theme)
- **Preference for ENUMs/Data Objects**: Arrays replaced with typed Data objects where possible

### 6.3. Implementation Notes

- All clarifications are reflected in Technical Context and Implementation Clarifications sections
- Test requirements updated to include TDD workflow, Filament panel, and auth page coverage
- Performance target specified as p95 latency for accurate measurement (same target for all load conditions)
- Theme preview page renamed from `/tailwindcss.catppuccin.com` to `/themes/preview`
- `ThemeData` DTO class specified for type-safe theme injection
- **Session 3 Clarifications (2025-11-25)**:
  - Validation timing: Validate on every access (whenever settings are read), not just on load
  - Auto-save feedback: Silent (no feedback) on success
  - Validation error handling: Prevent save, log error, notify user
  - Data retention policy: Retain until account deletion
  - Performance targets: Same target (p95 < 200ms) for all load conditions

-----

## 7. "MUST DEFINE" Requirements - Concrete Definitions

This section provides concrete definitions and implementation guidance for all "MUST DEFINE" requirements from the specification. These definitions resolve meta-requirements and provide actionable implementation details.

### 7.1. Data Model & Integrity Definitions

#### JSON Column Structure (FR-091)

**Structure Definition**:

```json
{
  "theme": "catppuccin",
  "flavor": "mocha",
  "accent": "primary"
}
```

**Requirements**:
- **Required fields**: `theme` (string), `flavor` (string), `accent` (string)
- **Optional fields**: None for theme preferences
- **Nested structure**: Flat structure (no nesting)
- **Nullable column**: `users.settings` column MUST remain nullable (allows null for new users)
- **Null handling**: All code paths MUST handle null consistently (lazy initialization with defaults)
- **Validation**: JSON structure MUST be validated before use (ensure required fields exist, no extra fields cause issues)
- **Documentation**: JSON structure MUST be formally documented in `data-model.md` and code PHPDoc

**Implementation**:
- Use `UserSettingsData` DTO for type-safe access
- Validate structure in `ThemeService::validateSettings()` before use
- Reject invalid structures (missing required fields, extra fields) and reset to defaults

#### Enum Serialization & Deserialization (FR-092)

**Serialization Requirements**:
- Enum values MUST serialize to JSON as string values matching enum cases exactly
- Example: `Theme::Catppuccin` → `"catppuccin"` (lowercase, matches enum case)
- Serialization MUST be verified to prevent data corruption

**Deserialization Failure Handling**:
- Invalid enum values in JSON MUST trigger validation and correction logic
- Corrupted data MUST be detected and auto-corrected to defaults
- Deserialization failures MUST log error (without exposing user data) and reset to defaults

**Implementation**:
- Use Spatie Laravel Data's built-in enum serialization
- Add validation in `UserSettingsData::from()` to catch deserialization failures
- Log deserialization failures in `ThemeService::validateSettings()`

#### Theme/Flavor Relationship Changes (FR-093, FR-098)

**Migration Strategy for Enum Relationship Changes**:
- **Scenario**: If enum relationships change after data is persisted (e.g., flavor removed, theme/flavor relationship changes)
- **Strategy**: Validation on every access detects invalid combinations and auto-corrects to defaults
- **Migration Path**: No database migration required; validation handles correction at runtime
- **Rollback**: If enum changes are reverted, validation will allow previously invalid combinations again
- **Data Transformation**: Invalid combinations are transformed to defaults (`Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`)

**Implementation**:
- `ThemeService::validateThemeCombination()` checks if flavor belongs to theme
- On invalid: Reset to defaults and persist correction
- Log correction events (without exposing user data) for analysis

#### State Synchronization (FR-096)

**State Synchronization Requirements**:
- **Reliable**: All components (User model, Livewire component, View Composer) MUST see consistent theme state
- **Immediate**: State changes MUST be visible immediately (<200ms) across all components
- **Consistency**: All update paths (Livewire component, direct model update, migration) MUST produce consistent results

**Implementation Strategy**:
- **Single Source of Truth**: Database (`users.settings` column) is the source of truth
- **Read Path**: All components read from database (via User model) on every access
- **Write Path**: All updates go through User model's `settings` property (ensures validation)
- **Cache Invalidation**: No caching of theme settings (read from database on every access ensures consistency)
- **Concurrent Updates**: Last write wins strategy (most recent save overwrites previous saves)

#### Data Lifecycle Management (FR-030)

**Cleanup Requirements**:
- **Orphaned Data**: No orphaned data (theme preferences are tied to user accounts)
- **Invalid Data**: Invalid data is auto-corrected on every access (no cleanup needed)
- **Account Deletion**: Theme preferences are deleted when user account is deleted (Laravel's `deleting` event)

**Implementation**:
- Use Laravel's model events: `User::deleting()` to clean up settings (or rely on cascade delete if configured)
- No separate cleanup job needed (data is tied to user lifecycle)

#### Database Indexing (FR-052)

**Indexing Requirements**:
- **Evaluation**: Evaluate if JSON column indexing is needed for query performance
- **Decision**: No indexing required for this feature (theme settings are read via user ID, which is already indexed)
- **Documentation**: Document decision in `data-model.md`

**Rationale**:
- Theme settings are accessed via `User::find($id)->settings` (user ID is primary key, already indexed)
- No queries filter by theme/flavor/accent values
- JSON column indexing would not improve performance for this use case

### 7.2. Performance Definitions

#### Performance Percentiles (FR-032)

**Percentile Targets**:
- **p50 (median)**: < 100ms (50% of requests)
- **p95**: < 200ms (95% of requests) - **Primary target (SC-002)**
- **p99**: < 300ms (99% of requests)
- **max**: < 500ms (worst case)

**Measurement Point**: From user click to visual feedback completion (DOM update, CSS application, browser paint)

**Implementation**:
- Use browser Performance API (`performance.mark()`, `performance.measure()`) for client-side measurement
- Record measurements in Telescope for analysis
- Alert on p95 > 200ms violations

#### Initial Page Load Performance (FR-110)

**Targets**:
- **Time to First Paint (TTFP)**: < 1s (with theme attributes set)
- **Time to Interactive (TTI)**: < 2s (with theme applied)
- **FOUC Prevention**: Theme attributes MUST be set within 50ms of page load (FR-035)

**Server-Side Theme Injection Performance**:
- **View Composer Overhead**: < 10ms (minimal overhead, single database query if authenticated)
- **Database Query Time**: < 5ms (user settings query, cached if user already loaded)
- **Unauthenticated Requests**: < 1ms (no database query, uses defaults immediately)

**Invalid Settings Overhead**:
- **Validation Time**: < 2ms (lightweight enum checks)
- **Correction Persistence**: < 10ms (single database update)

#### Database Performance (FR-111)

**Auto-Save Performance**:
- **Latency**: < 50ms (database write time)
- **Throughput**: Support 10 requests per 60 seconds per user (rate limit)
- **Overhead**: Auto-save MUST not significantly impact user experience (< 5% CPU increase)

**Settings Query Performance**:
- **Query Time**: < 5ms (single row lookup by user ID)
- **Caching**: User model may be cached by Laravel's authentication system (automatic)
- **Cache Strategy**: No explicit caching needed (Laravel handles user model caching)

**Concurrent Updates**:
- **Multiple Tabs**: Last write wins (no locking needed, simple strategy)
- **Simultaneous Saves**: Database handles concurrency (row-level locking)
- **Performance**: No degradation under concurrent updates (single row updates are fast)

**Validation Performance**:
- **Validation Overhead**: < 2ms (lightweight enum checks)
- **Correction Persistence**: < 10ms (single database update if correction needed)

#### Client-Side Performance (FR-112)

**JavaScript Execution**:
- **DOM Update Time**: < 50ms (attribute setting via `dataset` or `setAttribute`)
- **Attribute Setting Overhead**: < 1ms per attribute (3 attributes: theme, flavor, accent = < 3ms total)

**CSS Application**:
- **Attribute Selector Matching**: < 5ms (native browser performance)
- **Style Recalculation**: < 10ms (CSS attribute selectors are efficient)
- **No Reflow**: Theme changes use CSS variables (no layout shifts)

**Browser Rendering**:
- **Repaint Time**: < 50ms (CSS variable changes trigger repaint, not reflow)
- **Layout Shift Prevention**: No layout shifts (CSS variables don't affect layout)
- **Jank Prevention**: Hardware-accelerated CSS transitions (if used)

#### Device & Browser Performance (FR-113)

**Device Targets**:
- **Mobile**: Same target (p95 < 200ms) - performance MUST be acceptable on mobile devices
- **Tablet**: Same target (p95 < 200ms)
- **Desktop**: Same target (p95 < 200ms)

**Browser Compatibility**:
- **Chrome**: p95 < 200ms (primary target)
- **Firefox**: p95 < 200ms
- **Safari**: p95 < 200ms

**Implementation**:
- Test on real devices (mobile, tablet, desktop)
- Test on all major browsers (Chrome, Firefox, Safari)
- Use browser Performance API for measurement
- Alert on performance degradation on any device/browser

#### Network & Scalability (FR-114)

**Asset Sizes**:
- **CSS File Size**: < 50KB (gzipped) for theme CSS
- **JavaScript Bundle Size**: < 10KB (gzipped) for theme JS (minimal, mostly inline)
- **Asset Loading**: CSS/JS loaded once per page (no additional requests for theme changes)

**Scalability Requirements**:
- **High User Load**: Performance target (p95 < 200ms) MUST be maintained under high load
- **Concurrent Theme Changes**: Support 100+ simultaneous theme changes per second (database can handle this)
- **Resource Usage**: Adding new themes MUST not degrade performance (CSS attribute selectors scale linearly)

**Concurrent User Load**:
- **Multiple Users**: Performance target (p95 < 200ms) MUST be maintained
- **Database Load**: Single row updates per user (minimal database load)
- **No Bottlenecks**: Theme changes are user-specific (no shared resources)

#### Performance Degradation (FR-115)

**Degradation Scenarios**:
- **Database Slowdown**: Graceful degradation (show loading state, retry with exponential backoff)
- **Network Latency**: Optimistic updates (client-side first, server sync in background)
- **High Load**: Same performance target (p95 < 200ms) - if not met, it's a performance issue to address

**Graceful Degradation Strategy**:
- **Fail Fast**: If database is unavailable, show error message (don't hang)
- **Retry Logic**: 5 retries with exponential backoff (1s, 2s, 4s, 8s, 16s)
- **User Feedback**: Notify user of failures (error message, retry button)

#### Performance Testing (FR-116)

**Methodology**:
- **Load Testing**: Simulate 100+ concurrent users changing themes
- **Stress Testing**: Test under database load, network latency
- **Benchmark Testing**: Measure p50, p95, p99, max percentiles

**Test Scenarios**:
- **Normal Load**: 10 users changing themes simultaneously
- **High Load**: 100+ users changing themes simultaneously
- **Edge Cases**: Rapid successive changes, concurrent updates from multiple tabs

**Test Environment**:
- **Production-Like**: Tests MUST run in production-like environment (staging)
- **Real Devices**: Test on real mobile/tablet/desktop devices
- **Real Browsers**: Test on Chrome, Firefox, Safari

#### Performance Monitoring (FR-117)

**Tools**:
- **Laravel Telescope**: Performance markers, query time, request duration
- **Browser Performance API**: Client-side timing (DOM update, paint time)
- **Custom Events**: Theme change events with timing data

**Metrics**:
- **p50, p95, p99, max**: Percentile latencies for theme changes
- **Query Time**: Database query duration for settings retrieval
- **DOM Update Time**: Client-side attribute setting duration

**Data Collection**:
- **Storage**: Telescope database (Laravel's default)
- **Retention**: 7 days (Telescope default, configurable)
- **Queryability**: Telescope dashboard, API endpoints for export

**Dashboards**:
- **Real-Time**: Live dashboard showing current theme change performance
- **Historical**: 7-day trend of theme change latencies
- **Alerts**: Alert on p95 > 200ms (threshold violation)

**Alerting**:
- **Threshold**: p95 > 200ms for 5 minutes
- **Channels**: Email, Slack (configurable)
- **Severity**: Warning (not critical, but should be addressed)

#### Performance Optimization (FR-118)

**Optimization Guidelines**:
- **When to Optimize**: Only if p95 > 200ms target is not met
- **Trade-offs**: Prefer simplicity over premature optimization
- **Priority**: Client-side DOM updates are highest priority (user-perceived performance)

**Caching Strategy**:
- **Theme Data**: No caching (read from database on every access ensures consistency)
- **CSS/JS Assets**: Browser caching (standard HTTP cache headers)
- **Cache Invalidation**: Not applicable (no theme data caching)

**Lazy Loading**:
- **Theme Assets**: Not needed (CSS/JS loaded once per page)
- **Deferred Loading**: Not needed (theme code is minimal)

**Code Splitting**:
- **Theme Code**: Not needed (theme code is minimal, < 10KB)
- **Separate Bundles**: Not needed (no performance benefit)

**Optimization Priority**:
1. **Client-Side DOM Updates** (highest priority - user-perceived performance)
2. **Database Query Time** (if > 5ms, investigate)
3. **Server-Side Injection** (if > 10ms, investigate)

#### Preview Page Performance (FR-119)

**Initial Load Time**:
- **Target**: < 1s (same as main application)
- **Session Storage Read**: < 1ms (in-memory, no network)

**Theme Switching Latency**:
- **Target**: p95 < 200ms (same as authenticated settings)
- **Session Storage Write**: < 1ms (in-memory, no network)

**Network Conditions**:
- **Slow Network**: Same target (p95 < 200ms) - client-side only, no network requests
- **Offline Mode**: Theme changes work offline (session storage, no server dependency)

**Performance Consistency**:
- **Match Authenticated Settings**: Preview page MUST match authenticated settings page performance (same code paths, same targets)

#### Performance Acceptance Criteria (FR-120)

**Operations**:
- **Theme Change**: p95 < 200ms (from click to visual feedback)
- **Page Load**: TTFP < 1s, TTI < 2s (with theme attributes set)
- **Validation**: < 2ms (lightweight, on every access)

**Conditions**:
- **Normal Load**: p95 < 200ms
- **High Load**: p95 < 200ms (same target)

**Regression Thresholds**:
- **Acceptable Degradation**: < 10% increase in p95 latency (e.g., p95 < 220ms is acceptable)
- **Unacceptable Degradation**: > 10% increase (e.g., p95 > 220ms requires investigation)

### 7.3. Observability Definitions

#### Event Data Structure (FR-036)

**Event Structure**:

```json
{
  "event_type": "theme_changed",
  "timestamp": "2025-11-25T12:00:00Z",
  "timezone": "UTC",
  "user_id": 123,
  "session_id": "abc123",
  "request_id": "req-456",
  "trace_id": "trace-789",
  "previous_theme": "catppuccin",
  "previous_flavor": "mocha",
  "previous_accent": "primary",
  "new_theme": "kanagawa",
  "new_flavor": "wave",
  "new_accent": "blue",
  "source": "livewire_component",
  "performance": {
    "dom_update_time_ms": 45,
    "total_time_ms": 120
  }
}
```

**Required Fields**:
- `event_type` (string): Type of event (theme_changed, validation_corrected, preview_interaction)
- `timestamp` (ISO 8601): When event occurred
- `timezone` (string): Timezone of timestamp (always UTC)

**Optional Fields**:
- `user_id` (integer): User ID (null for preview page)
- `session_id` (string): Session identifier
- `request_id` (string): Request identifier (for correlation)
- `trace_id` (string): Trace identifier (for distributed tracing)
- `performance` (object): Performance metrics

**Event Correlation**:
- Use `request_id` to link related events (same request)
- Use `trace_id` for distributed tracing (if implemented)
- Use `session_id` to link preview page interactions

**Event Sampling**:
- **All Events Tracked**: No sampling (theme events are infrequent, low overhead)
- **Rate Limiting**: Already handled by auto-save rate limiting (10 requests per 60 seconds)

#### Log Levels (FR-038)

**Log Level Requirements**:
- **info**: Theme changes, preview interactions (normal operation)
- **warning**: Validation corrections, retry attempts (recoverable issues)
- **error**: Save failures, deserialization failures (unrecoverable issues)
- **debug**: Performance markers, detailed timing (development only)

**Log Format**:
- **Structure**: JSON (structured logging)
- **Fields**: `level`, `message`, `context`, `timestamp`, `user_id` (if applicable)
- **Consistency**: All logs use same format, same field names

**Log Aggregation**:
- **Storage**: Laravel logs (default: `storage/logs/laravel.log`)
- **Aggregation**: Telescope (for queryable logs)
- **Search**: Telescope dashboard (filter by event type, user, timestamp)

**Log Rotation**:
- **Retention**: 7 days (Laravel default, configurable)
- **Archival**: Not needed (short retention period)
- **Access**: Telescope dashboard for historical logs

#### Data Retention Policies (FR-039, FR-099)

**Observability Data Retention**:
- **Telescope Logs**: 7 days (default, configurable via `TELESCOPE_DB_RETENTION_DAYS`)
- **Telescope Metrics**: 7 days (default, configurable)
- **Application Logs**: 7 days (Laravel default, configurable)
- **Performance Data**: 7 days (stored in Telescope)

**Deletion Schedule**:
- **Automatic**: Telescope auto-deletes old records (configurable)
- **Manual**: Can be triggered via Telescope commands

#### Telescope Configuration (FR-099)

**What to Capture**:
- **Request/Response**: All HTTP requests (default Telescope behavior)
- **Logs**: All application logs (default Telescope behavior)
- **Queries**: Database queries (default Telescope behavior)
- **Events**: Custom theme events (via `Telescope::recordEvent()`)
- **Performance Markers**: Custom performance markers (via `Telescope::recordPerformance()`)

**Dashboard Configuration**:
- **Default Dashboard**: Use Telescope's default dashboard
- **Custom Views**: Create custom views for theme events (filter by `event_type = 'theme_changed'`)
- **Metrics Displayed**: p50, p95, p99 latencies, event counts, error rates

**Filtering & Search**:
- **By Event Type**: Filter by `event_type` (theme_changed, validation_corrected, etc.)
- **By User**: Filter by `user_id` (for authenticated users)
- **By Session**: Filter by `session_id` (for preview page)
- **By Time Range**: Filter by `timestamp` (last hour, last day, last week)

**Performance Impact**:
- **Overhead**: < 5% (Telescope is lightweight)
- **Acceptable**: Yes (observability is valuable, overhead is minimal)

#### Horizon Configuration (FR-100)

**When to Configure**:
- **Current**: Not needed (no queues used for theme operations)
- **Future**: Configure if background jobs are introduced (e.g., theme broadcasting, analytics)

**Queue Metrics** (if queues are used):
- **Relevant Metrics**: Queue length, job processing time, failed jobs
- **When Used**: Only if theme operations are queued (not currently planned)

**Dashboard Setup** (if queues are used):
- **Default Dashboard**: Use Horizon's default dashboard
- **Metrics Displayed**: Queue length, job processing time, failed jobs

**Handling When No Queues**:
- **Horizon Optional**: Horizon is optional (only needed if queues are used)
- **Disable if Not Used**: Can be disabled if no queues are used (no impact on theming)

#### Performance Metric Collection (FR-101)

**Metrics to Collect**:
- **p50, p95, p99, max**: Percentile latencies for theme changes
- **DOM Update Time**: Client-side attribute setting duration
- **Database Query Time**: Settings retrieval duration
- **Total Time**: End-to-end time (click to visual feedback)

**Instrumentation Implementation**:
- **Custom Events**: Use `Telescope::recordEvent()` for theme change events
- **Timing Calls**: Use browser Performance API for client-side timing
- **Where to Instrument**: Livewire component `updated()` method, View Composer

**Queryability**:
- **Telescope Dashboards**: Query via Telescope dashboard (filter by event type)
- **API Endpoints**: Telescope provides API endpoints for export
- **Export Capabilities**: Export to CSV/JSON via Telescope API

**Regression Detection**:
- **Alerts**: Alert on p95 > 200ms (threshold violation)
- **Thresholds**: p95 < 200ms (primary target)
- **Notification**: Email, Slack (configurable)

#### Invalid Theme Combination Tracking (FR-102)

**What to Record**:
- **Invalid Combination**: What was invalid (theme, flavor, accent values)
- **Correction**: What it was corrected to (defaults: Catppuccin Mocha Primary)
- **User ID**: User ID (if authenticated, null for preview)
- **Timestamp**: When correction occurred

**Correction Frequency Tracking**:
- **Per User**: Count corrections per user (identify users with corrupted data)
- **Globally**: Count total corrections (identify systemic issues)

**Alerting on High Correction Rates**:
- **Threshold**: > 10 corrections per hour globally (indicates systemic issue)
- **Notification**: Email, Slack (configurable)
- **Severity**: Warning (not critical, but should be investigated)

#### Preview Page Interaction Tracking (FR-103)

**Interactions to Track**:
- **Theme Changes**: When visitor changes theme/flavor/accent
- **Navigation**: When visitor navigates away from preview page
- **Session Duration**: How long visitor stays on preview page

**Usage Patterns**:
- **Visitor Count**: How many visitors use preview page
- **Theme Preferences**: Which themes are most popular (for product insights)
- **Session Duration**: Average time on preview page

**Conversion Correlation**:
- **Sign-Up Tracking**: Link preview interactions to sign-ups (if visitor signs up after preview)
- **Authenticated Usage**: Track if preview visitors later use authenticated settings

**Performance Tracking**:
- **Load Times**: Preview page load time
- **Interaction Latency**: Theme switching latency on preview page
- **Error Rates**: Any errors on preview page

#### Error Context (FR-104)

**Error Log Requirements**:
- **Stack Traces**: Full stack trace for errors (for debugging)
- **Request Context**: Request ID, method, URL, headers (for correlation)
- **User Context**: User ID (if authenticated), session ID (for preview)
- **Theme Context**: Current theme/flavor/accent (if applicable)

**Sensitive Data Exclusion**:
- **No PII**: Do not log passwords, tokens, personal information
- **Anonymized**: Log event type and correction action, not actual theme values or user identifiers (for privacy)

**Error Alerting**:
- **When to Alert**: On error level logs (unrecoverable issues)
- **Severity Levels**: Critical (system down), Warning (recoverable), Info (informational)
- **Notification Channels**: Email, Slack, PagerDuty (configurable)

**Error Rate Tracking**:
- **Error Frequency**: Count errors per hour/day
- **Error Types**: Categorize errors (validation, deserialization, save failures)
- **Resolution Tracking**: Track error resolution (manual process)

#### Observability Dashboards (FR-105)

**Dashboards Needed**:
- **Theme Events Dashboard**: Theme changes, validation corrections, preview interactions
- **Performance Dashboard**: p50, p95, p99 latencies, DOM update times
- **Error Dashboard**: Error rates, error types, resolution status

**Real-Time vs. Historical**:
- **Real-Time**: Live dashboard showing current events (last 5 minutes)
- **Historical**: 7-day trend of events, performance, errors
- **Time Ranges**: Last hour, last day, last week (configurable)

**Access Control**:
- **Authentication**: Telescope requires authentication (Laravel's default)
- **Authorization**: Only authorized users can view dashboards (Laravel's authorization)
- **Roles**: Admin users can view all dashboards

#### Alert Conditions (FR-106)

**Alert Conditions**:
- **p95 > 200ms**: Performance degradation (threshold violation)
- **Error Rate > 10/hour**: High error rate (systemic issue)
- **Correction Rate > 10/hour**: High validation correction rate (data corruption)
- **Database Unavailable**: Database connection failures

**Alert Channels**:
- **Email**: Send email to team (configurable recipients)
- **Slack**: Send Slack message to channel (configurable)
- **PagerDuty**: Escalate to on-call (for critical alerts)

**Alert Severity**:
- **Critical**: System down, database unavailable
- **Warning**: Performance degradation, high error rates
- **Info**: High correction rates (informational)

**Alert Deduplication**:
- **Grouping**: Group similar alerts (e.g., multiple p95 violations in 5 minutes = 1 alert)
- **Rate Limiting**: Max 1 alert per condition per 5 minutes (prevent alert storms)

#### Observability Testing (FR-107)

**Testing Requirements**:
- **Event Capture**: Verify events are captured (check Telescope dashboard)
- **Metrics Recording**: Verify metrics are recorded (check Telescope dashboard)
- **Log Levels**: Verify correct log levels are used (check application logs)

**Acceptance Criteria**:
- **Events Visible**: Theme events visible in Telescope dashboard
- **Metrics Queryable**: Performance metrics queryable via Telescope API
- **Logs Searchable**: Logs searchable via Telescope dashboard

**Regression Testing**:
- **Observability Tests**: Include observability tests in test suite
- **Verify Instrumentation**: Verify instrumentation doesn't break when code changes
- **Performance Impact**: Verify observability doesn't impact performance (> 5% overhead)

#### Telescope & Horizon Setup (FR-108)

**Installation**:
- **Telescope**: Already installed (Laravel default)
- **Horizon**: Already installed (Laravel default, but optional if no queues)

**Configuration Steps**:
1. **Telescope**: Configure `TELESCOPE_ENABLED=true` in `.env`
2. **Telescope**: Configure `TELESCOPE_DB_RETENTION_DAYS=7` in `.env`
3. **Horizon**: Configure only if queues are used (not needed for current implementation)

**Environment Setup**:
- **Development**: Telescope enabled, full logging
- **Staging**: Telescope enabled, full logging (production-like)
- **Production**: Telescope enabled, full logging (with access control)

**Feature Flags**:
- **Telescope**: Can be disabled via `TELESCOPE_ENABLED=false` (not recommended)
- **Horizon**: Can be disabled if no queues are used (no impact)

**Performance Overhead**:
- **Telescope**: < 5% overhead (acceptable)
- **Horizon**: 0% overhead if not used (no impact)

### 7.4. UX Definitions

#### Toast Notification Requirements (FR-045)

**Content**:
- **Success**: No toast (silent auto-save, FR-082)
- **Error**: "Theme update failed. Please try again." (user-friendly message)
- **Retry**: "Retrying theme update..." (during retry attempts)

**Timing**:
- **Duration**: 3 seconds (for error/retry toasts)
- **Positioning**: Bottom-right (standard position, non-intrusive)

**Accessibility**:
- **Screen Reader**: Announce via ARIA live region
- **Keyboard Dismissible**: ESC key dismisses toast
- **Contrast**: Sufficient contrast (WCAG AA)

**Consistency**:
- **Styling**: Consistent across all pages (use Flux toast component)
- **Positioning**: Same position on all pages
- **Duration**: Same duration (3 seconds) for all toasts

#### Interaction Requirements (FR-079)

**Theme Selection Controls**:
- **Type**: Radio buttons (mutually exclusive, clear selection)
- **Layout**: Horizontal layout (Theme → Flavor → Accent, left to right)
- **Grouping**: Group related controls (Theme group, Flavor group, Accent group)

**Intuitive & Discoverable**:
- **Labels**: Clear labels ("Theme", "Flavor", "Accent")
- **Visual Previews**: Color swatches for each theme/flavor/accent
- **Logical Grouping**: Related controls grouped visually (fieldset/legend)

**Settings Page Discoverability**:
- **Navigation**: Link in user menu (Settings → Appearance)
- **Breadcrumbs**: Clear navigation path (Settings > Appearance)

#### Preview Page User Flow (FR-080)

**Discoverability**:
- **Public Link**: Link in footer or public navigation ("Try Themes" or "Theme Preview")
- **Purpose**: Clearly communicate purpose ("Preview all available themes without signing up")

**Layout**:
- **Match Authenticated Settings**: Same layout as authenticated settings page (for consistency)
- **Visual Indication**: "Preview Mode" banner at top (indicates temporary changes)

**Temporary Changes Messaging**:
- **Banner**: "Preview Mode - Changes are temporary and will reset when you leave this page"
- **Styling**: Different styling (e.g., subtle border or background) to indicate preview mode

#### Live Preview Visual Requirements (FR-081)

**Instant Color Changes**:
- **No Flicker**: Smooth color transitions (no white flash)
- **No Layout Shifts**: CSS variables don't affect layout (no shifts)

**Smooth Transitions**:
- **Hardware-Accelerated**: Use CSS transitions (GPU-accelerated)
- **Duration**: 150ms transition (feels instant but smooth)
- **Easing**: `ease-out` (natural feeling)

**Visual Confirmation**:
- **Color Change**: Immediate color change (visual confirmation)
- **No Animation**: No explicit animation needed (color change is confirmation enough)

#### Initial Page Load & Loading States (FR-083)

**FOUC Prevention**:
- **Server-Side Injection**: Theme attributes set in HTML before CSS loads (prevents FOUC)
- **Target**: Attributes set within 50ms of page load (FR-035)

**Loading States**:
- **Slow Connections**: Server-side injection handles this (no loading state needed)
- **No Layout Shift**: Attributes set before render (no shift)

**User Feedback**:
- **Immediate Render**: Page renders with theme immediately (no loading spinner needed)
- **Skeleton**: Not needed (theme attributes are set server-side)

#### Mobile Device Requirements (FR-084)

**Touch Targets**:
- **Minimum Size**: 44x44px (WCAG AA requirement)
- **Spacing**: Adequate spacing between controls (prevent mis-taps)

**Layout Adaptation**:
- **Small Screens**: Stack controls vertically on mobile (Theme, Flavor, Accent stacked)
- **Tablet**: Horizontal layout (same as desktop)
- **Desktop**: Horizontal layout (Theme → Flavor → Accent)

**Full Usability**:
- **All Features**: All theme selection features work on mobile
- **Preview Page**: Preview page works on mobile (same functionality)

#### Visual Hierarchy (FR-085)

**Control Prominence**:
- **Theme**: Most prominent (largest, first in layout)
- **Flavor**: Second most prominent (medium size, second in layout)
- **Accent**: Least prominent (smallest, last in layout)

**Visual Organization**:
- **Grouping**: Group related controls (fieldset/legend)
- **Alignment**: Left-aligned labels, controls aligned
- **Visual Relationships**: Clear visual relationships (spacing, borders)

**Theme Previews**:
- **Color Swatches**: Show color swatch for each theme/flavor/accent
- **Visual Examples**: Show example of each theme combination (screenshot or live preview)

#### State Transitions (FR-086)

**Smooth Color Transitions**:
- **CSS Transitions**: 150ms transition on color properties
- **Easing**: `ease-out` (natural feeling)
- **No Fade**: No fade effects (color change is enough)

**Persistence**:
- **Across Navigation**: Theme persists across page navigation (server-side injection)
- **No Flicker**: No flicker on navigation (attributes set before render)
- **No Reset**: Theme doesn't reset to default (persists correctly)

**Session Consistency**:
- **During Session**: Theme remains consistent during user session
- **After Refresh**: Theme persists after page refresh (saved to database)

#### Consistency Requirements (FR-087)

**Filament vs. Main Application**:
- **Same Theme**: Filament panels use same theme as main application
- **Same Colors**: Same color scheme (zinc palette mapped correctly)
- **Same Transitions**: Same transition behavior (if transitions are used)

**Fortify vs. Main Application**:
- **Same Theme**: Auth pages use same theme as main application
- **Same Colors**: Same color scheme
- **Same Transitions**: Same transition behavior

**Preview vs. Authenticated Settings**:
- **Same Visual Behavior**: Preview page matches authenticated settings (same color changes, same transitions)
- **Same Performance**: Same performance targets (p95 < 200ms)

#### Perceived Performance (FR-088)

**Instant vs. Animated**:
- **Instant**: Color changes are instant (no animation delay)
- **Smooth**: But smooth (150ms CSS transition for polish)

**Responsive Feel**:
- **Optimistic Updates**: Client-side updates first (instant visual feedback)
- **Server Sync**: Server sync in background (auto-save)
- **Network Latency**: Feels responsive even with network latency (client-side first)

**Visual Feedback**:
- **Instant Visual Update**: Color changes immediately (visual confirmation)
- **No Loading Indicators**: No loading spinner (instant updates)

#### Edge Case UX (FR-089)

**No Saved Preferences**:
- **Default Theme**: Default theme applied (Catppuccin Mocha Primary)
- **Clear Indication**: No special indication needed (default is normal state)

**Corrupted Data**:
- **Silent Correction**: Data corrected silently (no user notification)
- **Visual Reflection**: UI reflects correction (shows corrected theme)

**Concurrent Changes**:
- **Last Write Wins**: Most recent change persists (simple, predictable)
- **No Real-Time Sync**: Tabs don't sync in real-time (sync on next page load)
- **User Expectation**: Users understand last write wins (standard behavior)

#### UX Success Metrics (FR-090)

**Beyond Latency**:
- **User Satisfaction**: User surveys (optional, not required for MVP)
- **Error Rates**: Track error rates (via observability)
- **Task Completion Time**: Time to change theme (target: < 5 seconds)

**Measurable Success Criteria**:
- **Latency**: p95 < 200ms (SC-002)
- **Error Rate**: < 1% (target, not formal requirement)
- **Task Completion**: < 5 seconds (target, not formal requirement)

### 7.5. Security Definitions

#### Resource Exhaustion Limits (FR-058)

**Memory Limits**:
- **PHP Memory Limit**: 128MB (Laravel default, sufficient for theme operations)
- **JSON Payload Size**: 64KB (65,535 bytes) - reject oversized (FR-029)

**CPU Limits**:
- **Theme Operations**: Lightweight (enum checks, single database query)
- **No CPU Intensive Operations**: Theme changes don't require heavy computation

**Database Connection Limits**:
- **Connection Pool**: Laravel handles connection pooling (default configuration)
- **No Special Limits**: Standard Laravel database limits apply

**JSON Payload Size Handling**:
- **Validation**: Reject payloads > 64KB (prevent DoS)
- **Error Message**: "Settings data too large. Please contact support." (user-friendly)

#### Security Testing (FR-075)

**Penetration Testing**:
- **XSS Testing**: Test for XSS vulnerabilities (attribute injection, CSS injection)
- **CSRF Testing**: Verify CSRF protection (Livewire handles this automatically)
- **Input Validation**: Test invalid enum values, oversized payloads

**Vulnerability Scanning**:
- **Dependency Scanning**: Scan dependencies for vulnerabilities (composer audit)
- **Code Scanning**: Static analysis (Larastan, PHPStan)

**Security Code Review**:
- **Review Theme Code**: Review all theme-related code for security issues
- **Review Validation**: Review validation logic for bypasses

#### Security Acceptance Criteria (FR-076)

**Measurable Criteria**:
- **All Inputs Validated**: All theme/flavor/accent inputs validated against enums
- **All Outputs Encoded**: All theme data attributes HTML-encoded (Laravel default)
- **No XSS Vulnerabilities**: No XSS vulnerabilities found in security testing
- **CSRF Protection Verified**: CSRF protection verified (Livewire handles this)
- **Rate Limiting Active**: Rate limiting active (10 requests per 60 seconds)

#### Audit Logging (FR-077)

**Security-Relevant Events**:
- **Failed Validations**: Log failed validation attempts (without exposing user data)
- **Unauthorized Access**: Log unauthorized access attempts (if applicable)
- **Rate Limit Violations**: Log rate limit violations (for abuse detection)

**Log Requirements**:
- **Separate Logs**: Security audit logs separate from application logs
- **Retention**: Retain per compliance requirements (7 days default, configurable)
- **Traceability**: Theme preference changes traceable (log user ID, timestamp, previous value, new value, source IP)

**Privacy**:
- **No PII**: Do not log passwords, tokens, personal information
- **Anonymized**: Log event type and correction action, not actual theme values or user identifiers

-----

## 8. Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|--------------------------------------|
| e.g., 4th project | current need | why 3 projects insufficient |
| e.g., Repository pattern | specific problem | why direct DB access insufficient |

-----
