# Implementation Plan: Theming Engine

**Branch**: `006-theming-engine` | **Date**: 2025-11-25 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/006-theming-engine/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## 1. Summary

Complete the implementation of a Theming Engine that allows users to customize the visual appearance of the application (Theme, Flavor, Accent) with immediate live preview and auto-save. The system uses a hybrid approach: server-side injection of data attributes for initial page load via View Composer, plus client-side JavaScript updates for instant preview via Livewire. Themes apply globally across all pages including Filament admin panels and authentication pages. Invalid theme combinations are silently auto-corrected on every access. A public theme preview page (`/themes/preview`) allows unauthenticated visitors to preview all 15 themes with temporary session storage.

**Technical Approach**:

- Server-side: View Composer in `AppServiceProvider` injects theme data into all views
- Client-side: Livewire component updates DOM attributes via `$this->js()` for <200ms live preview
- CSS: Attribute selectors (`[data-theme="catppuccin"][data-flavor="mocha"][data-accent="primary"]`) apply theme colors
- Accent Mapping: Hybrid approach - CSS variables for colors, PHP `ThemeAccentMapper` service for validation
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
- CSS must use attribute selectors: `[data-theme="catppuccin"][data-flavor="mocha"][data-accent="primary"]` (not CSS classes or dynamically injected variables)
- Accent colors are theme-specific and must be mapped to framework color systems via `ThemeAccentMapper` service
- Invalid theme combinations must be silently auto-corrected on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.)
- Default theme values: `Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary` (explicit defaults, no ambiguity)
- Flavor selector hidden when theme has only one flavor option
- Accent defaults to "Primary" when theme is selected (or first available if "Primary" doesn't exist)
- `ThemeAccentMapper` service failures must fallback to default theme with error logging
- Session expiration during auto-save: complete save if user still authenticated, otherwise discard silently

**Scale/Scope**:

- **15 themes total**:

  - **Global Developer Themes (10)**: Catppuccin, Tokyo Night, Dracula, Kanagawa, Gruvbox, Nord, Rosé Pine, One Dark Pro, Monokai Pro, Solarized
  - **UK Authentic Design System Themes (5)**: GOV.UK, Transport for London, NHS Digital, Financial Times, The Guardian

- **Flavors vary by theme**:
  - Catppuccin: Latte (light), Frappé (dark), Macchiato (dark), Mocha (dark) - 4 flavors
  - Tokyo Night: Night (dark), Day (light) - 2 flavors
  - Kanagawa: Wave (dark), Lotus (light) - 2 flavors
  - Gruvbox: Dark, Light - 2 flavors
  - Solarized: Dark, Light - 2 flavors
  - Dracula, Nord, Rosé Pine, One Dark Pro, Monokai Pro: Single flavor each (dark)
  - UK Authentic Themes: Single flavor each (all light mode)
- **Accent colors**: Theme-specific (each theme defines its own accent options, mapped via `ThemeAccentMapper` service)
- Global application across all pages:
  - Main application pages (all Folio pages)
  - Filament admin panels (all Filament panel views)
  - Authentication pages (all Fortify auth views: login, register, password reset, etc.)
  - Public pages (all Folio pages accessible without authentication, including the theme preview page)

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
  - Session expiration handling: complete save if authenticated, otherwise discard silently
- ✅ **Performance**:
  - Server-side injection prevents FOUC
  - Client-side updates <200ms via direct DOM manipulation
  - CSS attribute selectors use native browser performance

**No violations detected. All gates pass.**

-----

## 4. Project Structure

### 4.1. Documentation (this feature)

```log
specs/006-theming-engine/
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
│   ├── Theme.php                     # Existing: Theme enum (15 themes: 10 global developer + 5 UK authentic)
│   ├── ThemeFlavor.php               # Existing: Flavor enum (varies by theme)
│   └── ThemeAccent.php               # Existing: Accent enum (theme-specific accents)
├── Models/
│   └── User.php                      # Existing: User model with settings cast
├── Providers/
│   └── AppServiceProvider.php        # Modify: Add server-side theme injection via View Composer
└── Services/
    └── Theme/
        ├── ThemeService.php          # NEW: Theme validation (on every access) and default resolution
        └── ThemeAccentMapper.php     # NEW: Theme-specific accent validation and CSS variable name generation

resources/
├── css/
│   ├── app.css                       # Existing: Main CSS with theme attribute selectors
│   └── themes/
│       └── all-themes.css            # NEW: All 15 theme definitions with accent CSS variables
├── js/
│   └── app.js                        # Modify: Enhance client-side theme initialization
└── views/
    ├── livewire/
    │   └── settings/
    │       └── appearance.blade.php  # Existing: Appearance settings UI (already implements live preview)
    └── pages/
        └── themes/
            └── preview.blade.php      # NEW: Theme preview page (Folio route: /themes/preview)

tests/
├── Feature/
│   ├── Theme/
│   │   ├── ThemePersistenceTest.php  # NEW: Test theme persistence and retrieval
│   │   ├── ThemeValidationTest.php   # NEW: Test invalid theme combination handling
│   │   ├── ThemeGlobalApplicationTest.php  # NEW: Test theme applies globally (includes Filament panels and auth pages)
│   │   ├── ThemeAccentMapperTest.php  # NEW: Test theme-specific accent validation
│   │   ├── ThemeServiceFailureTest.php  # NEW: Test ThemeAccentMapper service failure handling
│   │   ├── ThemeSessionExpirationTest.php  # NEW: Test session expiration during auto-save
│   │   └── ThemePerformanceTest.php  # NEW: Test p95 latency < 200ms for theme changes (optional)
│   └── ThemePreview/
│       └── ThemePreviewPageTest.php  # NEW: Test public theme preview page functionality
└── Unit/
    └── Services/
        └── Theme/
            ├── ThemeServiceTest.php   # NEW: Test theme validation logic
            └── ThemeAccentMapperTest.php  # NEW: Test accent mapper service
```

**Structure Decision**: Laravel monolith structure. Theming code organized for logical consistency and maintainability:

- **Data Objects**: `app/Data/` - All theme-related DTOs (UserSettingsData, ThemeData)
- **Enums**: `app/Enums/` - All theme-related enums (Theme, ThemeFlavor, ThemeAccent)
- **Services**: `app/Services/Theme/` - Theme-related business logic (validation, resolution, accent mapping)
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
- User selects a new theme (accent defaults to "Primary", or first available if "Primary" doesn't exist)
- `ThemeAccentMapper` service fails or throws exceptions (fallback with error logging)

### 5.2. Theme Preview Page Route Specification

**Route Details** (resolves underspecification E2, E3):

- **File Path**: `resources/views/pages/themes/preview.blade.php`
- **Route**: Folio page route (auto-generated from file path)
- **URL Path**: `/themes/preview`
- **Middleware**: None (public access, no authentication required)
- **Access**: Unauthenticated visitors can access without login
- **Themes Displayed**: All 15 themes organized by category (Global Developer Themes and UK Authentic Design System Themes)

### 5.3. Shared Asset Strategy

- Preview page reuses production `app.css` / `app.js` bundles for exact visual parity.
- Preview-specific session storage logic is injected via a small inline script within `resources/views/pages/themes/preview.blade.php`.
- Preview route should set shorter cache headers to prevent experiments from persisting across navigation while keeping CDN behavior safe for the main app.
- Any additional preview-only assets must be feature-flagged or conditionally loaded to avoid bloating the production bundle.

### 5.4. Accent Color Implementation

**Theme-Specific Accents** (from clarifications):

- Each theme defines its own set of accent color options
- Accent colors are mapped to component framework color systems (Flux 'zinc' palette, Filament 'gray' palette)
- **Hybrid Approach**:
  - CSS files define accent color variables (e.g., `--accent-flux-zinc-500`, `--accent-filament-gray-500`) selected by data attributes
  - PHP `ThemeAccentMapper` service provides type-safe accent validation, runtime queries for available accents per theme, and helper methods for CSS variable name generation
- Accent selector updates reactively when theme changes (similar to flavor selector)
- Accent defaults to "Primary" when theme is selected (or first available if "Primary" doesn't exist)
- **Service Failure Handling**: When `ThemeAccentMapper` service fails or throws exceptions, the system MUST fallback to default theme (`Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`), log the error for debugging, and continue application execution without exposing the error to users. This ensures graceful degradation and prevents service failures from breaking theme application.

### 5.5. UI Selector Behavior

**Flavor Selector** (from clarifications):

- Hidden when theme has only one flavor option
- Visible only when selected theme has multiple flavors
- Reduces UI clutter and clearly indicates theme flavor variations

**Accent Selector**:

- Updates reactively when theme changes (shows only accents valid for selected theme)
- Follows same pattern as flavor selector (can be hidden if theme has only one accent, though this is unlikely)

**Reset to Default Control** (from clarifications):

- A "Reset to Default" button/control MUST be provided that resets theme to `Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`
- The reset control MUST be conditionally visible: displayed only when the user's current theme selection differs from the default theme, hidden when the user's selection matches the default
- This provides clear visual feedback about the current state and avoids unnecessary UI clutter when already at default

### 5.6. CSS Implementation Details

**Clarification** (resolves terminology F2):

CSS uses **attribute selectors only**, not CSS classes or dynamically injected CSS variables. Implementation pattern:

```css
[data-theme="catppuccin"][data-flavor="mocha"][data-accent="primary"] {
  --color-zinc-900: #1e1e2e;
  --accent-flux-zinc-500: #cba6f7;
  --accent-filament-gray-500: #cba6f7;
  /* CSS custom properties defined in attribute selector rules */
}
```

CSS custom properties (variables) are defined statically in CSS files, selected by data attributes. Accent colors are defined as CSS variables (e.g., `--accent-flux-zinc-500`, `--accent-filament-gray-500`) in theme CSS files, and components reference these variables directly. No dynamic CSS injection or class toggling required.

### 5.7. Validation & Error Handling

**Validation Timing** (from Session clarifications):

- **Validate on every access**: Theme/flavor/accent combinations MUST be validated whenever settings are read (View Composer, Livewire mount, direct model access, etc.), not just on load. This ensures data integrity and handles edge cases (corruption, enum changes, migrations). The performance impact is minimal since validation is lightweight.

**Validation Error Handling** (from Session clarifications):

- **During save**: If validation fails during save, the system MUST prevent save, log error, and notify user with a user-friendly error message. This provides complete error handling and aligns with FR-044 (user notification on failures).

**Auto-save Feedback** (from Session clarifications):

- **Silent on success**: Auto-save provides no visual feedback when it succeeds. This provides clean UX and avoids notification fatigue. Users can verify persistence by refreshing the page.

**Data Retention Policy** (from Session clarifications):

- **Retain until account deletion**: Theme preferences are deleted when user account is deleted. This is simple and aligns with user data lifecycle management.

**Service Failure Handling** (from Session clarifications):

- **ThemeAccentMapper service failures**: When the `ThemeAccentMapper` service fails or throws exceptions, the system MUST fallback to default theme (`Theme::Catppuccin`, `ThemeFlavor::Mocha`, `ThemeAccent::Primary`), log the error for debugging, and continue application execution without exposing the error to users. This ensures graceful degradation and prevents service failures from breaking theme application.

**Session Expiration Handling** (from Session clarifications):

- **During auto-save**: If session expires during auto-save, the system MUST attempt to complete the save if the user is still authenticated. If authentication has expired, the save MUST be discarded silently and the user MUST be required to re-authenticate on next interaction. This prevents user confusion from partial saves and ensures data integrity.

### 5.8. Performance Targets

**Performance Consistency** (from Session clarifications):

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
  - `tests/Unit/Services/Theme/` - Unit tests for theme services (ThemeService, ThemeAccentMapper)
  - `tests/Unit/Data/` - Unit tests for theme Data objects (if needed)
  - `tests/Unit/Enums/` - Unit tests for theme enums (if needed)

**Additional Test Coverage** (resolves coverage gaps I1, I2):

1. **ThemeAccentMapper Service**:
   - Test: Verify theme-specific accent validation
   - Test: Verify available accents per theme
   - Test: Verify CSS variable name generation
   - Test: Verify service failure handling (fallback to default theme, error logging)
   - Location: `tests/Unit/Services/Theme/ThemeAccentMapperTest.php` and `tests/Feature/Theme/ThemeAccentMapperTest.php`

2. **Filament Panel Theme Application**:
   - Test: Verify theme data attributes are present in Filament panel views
   - Test: Verify theme colors apply correctly in Filament components
   - Location: `tests/Feature/Theme/ThemeGlobalApplicationTest.php`
   - Acceptance: User Story 1, Acceptance Scenario 5 (already covers this, but add explicit Filament test)

3. **Authentication Page Theme Application**:
   - Test: Verify theme data attributes are present in Fortify auth views (login, register, password reset, etc.)
   - Test: Verify theme colors apply correctly in auth page components
   - Location: `tests/Feature/Theme/ThemeGlobalApplicationTest.php`
   - Acceptance: User Story 1, Acceptance Scenario 5 (already covers this, but add explicit auth page test)

4. **Performance Testing**:
   - Test: Measure p95 latency for theme change DOM updates
   - Target: p95 < 200ms
   - Location: `tests/Feature/Theme/ThemePerformanceTest.php` (optional, or include in existing tests)

5. **Theme Preview Page Testing**:
   - Test: Verify unauthenticated access to `/themes/preview`
   - Test: Verify all 15 themes are accessible on preview page
   - Test: Verify session storage behavior
   - Test: Verify theme changes reset on navigation
   - Location: `tests/Feature/ThemePreview/ThemePreviewPageTest.php`

6. **Service Failure Handling Testing**:
   - Test: Verify ThemeAccentMapper service failure fallback behavior
   - Test: Verify error logging when service fails
   - Test: Verify graceful degradation (no user-facing errors)
   - Location: `tests/Feature/Theme/ThemeServiceFailureTest.php`

7. **Session Expiration Testing**:
   - Test: Verify auto-save completes if user still authenticated after session expiration
   - Test: Verify auto-save discards silently if authentication expired
   - Test: Verify re-authentication required on next interaction
   - Location: `tests/Feature/Theme/ThemeSessionExpirationTest.php`

8. **Reset to Default Control Testing**:
   - Test: Verify reset control visibility (hidden when at default, visible when changed)
   - Test: Verify reset control functionality (resets to default theme)
   - Location: `tests/Feature/Theme/ThemePersistenceTest.php` (or new test file)

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
- ✅ **E3 (Underspecification)**: Theme preview page route set to `/themes/preview` for better maintainability
- ✅ **F1 (Terminology)**: All `isDark` references replaced with `isLight()` method (matches existing code)
- ✅ **F2 (Terminology)**: `themeData` changed from array to `ThemeData` DTO class
- ✅ **F3 (Terminology)**: `availableFlavors` documented as `array<ThemeFlavor>` with PHPDoc
- ✅ **I1 (Coverage Gap)**: TDD workflow explicitly mandated (FR-013 added to spec)
- ✅ **I2 (Coverage Gap)**: Test organization aligned with source code structure

### 6.2. Code Organization Improvements

- **Data Objects**: All theme DTOs in `app/Data/` (UserSettingsData, ThemeData)
- **Enums**: All theme enums in `app/Enums/` (Theme, ThemeFlavor, ThemeAccent)
- **Services**: Theme services organized in `app/Services/Theme/` subdirectory (ThemeService, ThemeAccentMapper)
- **Tests**: Tests organized to match source structure (Feature/Theme, Feature/ThemePreview, Unit/Services/Theme)
- **Preference for ENUMs/Data Objects**: Arrays replaced with typed Data objects where possible

### 6.3. Implementation Notes

- All clarifications are reflected in Technical Context and Implementation Clarifications sections
- Test requirements updated to include TDD workflow, Filament panel, auth page, and ThemeAccentMapper coverage
- Performance target specified as p95 latency for accurate measurement (same target for all load conditions)
- Theme preview page route set to `/themes/preview`
- `ThemeData` DTO class specified for type-safe theme injection
- **Theme-Specific Accents**: Each theme defines its own accent options, mapped via `ThemeAccentMapper` service
- **UI Behavior**: Flavor selector hidden when only one option exists; accent selector updates reactively
- **Reset to Default Control**: Conditionally visible (hidden when at default, visible when changed)
- **Service Failure Handling**: ThemeAccentMapper failures fallback to default theme with error logging
- **Session Expiration Handling**: Complete save if authenticated, otherwise discard silently
- **Session Clarifications (2025-11-25)**:
  - Validation timing: Validate on every access (whenever settings are read), not just on load
  - Auto-save feedback: Silent (no feedback) on success
  - Validation error handling: Prevent save, log error, notify user
  - Data retention policy: Retain until account deletion
  - Performance targets: Same target (p95 < 200ms) for all load conditions
  - Accent colors: Theme-specific with hybrid CSS + PHP service approach
  - Flavor selector: Hidden when only one option exists
  - Default accent: "Primary" with fallback to first available
  - Reset control: Conditionally visible based on current selection vs default
  - Service failure: Fallback to default theme with error logging
  - Session expiration: Complete save if authenticated, otherwise discard silently

-----

## 7. Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|--------------------------------------|
| N/A | N/A | N/A |
