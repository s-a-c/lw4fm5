# Tasks: Theming Engine

**Feature**: Theming Engine
**Status**: Draft
**Spec**: [spec.md](./spec.md)
**Plan**: [plan.md](./plan.md)

-----

<details>
<summary>Expand for Table of Contents</summary>

- [Tasks: Theming Engine](#tasks-theming-engine)
  - [1. Dependencies](#1-dependencies)
  - [2. Implementation Strategy](#2-implementation-strategy)
  - [3. Phase 1: Setup \& Data Structures](#3-phase-1-setup--data-structures)
  - [4. Phase 2: Foundation - Server-Side Injection](#4-phase-2-foundation---server-side-injection)
  - [5. Phase 3: User Story 1 - Personalize Application Appearance](#5-phase-3-user-story-1---personalize-application-appearance)
  - [6. Phase 4: User Story 2 - System Default Fallback \& Validation](#6-phase-4-user-story-2---system-default-fallback--validation)
  - [7. Phase 5: User Story 3 - Public Theme Preview](#7-phase-5-user-story-3---public-theme-preview)
  - [8. Phase 6: Polish \& Performance](#8-phase-6-polish--performance)

</details>

-----

## 1. Dependencies

- **Phase 1 (Setup)**: Blocks Phase 2.
- **Phase 2 (Foundation)**: Blocks Phase 3, 4, 5. (Server-side injection is the core mechanism).
- **Phase 3 (US1)**: Independent of US2, US3.
- **Phase 4 (US2)**: Depends on Phase 2 (Validation logic).
- **Phase 5 (US3)**: Depends on Phase 2 (Theme structure).

## 2. Implementation Strategy

- **TDD Enforced**: All implementation tasks must be preceded by a corresponding test task.
- **Hybrid Approach**: Implement server-side injection first (Foundation), then client-side reactivity (US1), then public preview (US3).
- **MVP**: Phases 1, 2, and 3 constitute the MVP.

-----

## 3. Phase 1: Setup & Data Structures

**Goal**: Initialize core data structures required for theming.

- [ ] T001 [P] [FR-005, FR-006] Create `ThemeData` DTO with properties and `isLight()` method in `app/Data/ThemeData.php`
- [ ] T002 [P] [FR-001] Verify existing Enums (`Theme`, `ThemeFlavor`, `ThemeAccent`) in `app/Enums/` and ensure all 15 themes are defined (10 global developer themes: Catppuccin, Tokyo Night, Dracula, Kanagawa, Gruvbox, Nord, Rosé Pine, One Dark Pro, Monokai Pro, Solarized; 5 UK authentic themes: GOV.UK, Transport for London, NHS Digital, Financial Times, The Guardian)
- [ ] T002b [P] [FR-002] Verify `ThemeFlavor` enum includes all flavors for all 15 themes (Catppuccin: 4 flavors, Tokyo Night: 2 flavors, Kanagawa: 2 flavors, Gruvbox: 2 flavors, Solarized: 2 flavors, single flavors for others)
- [ ] T002c [P] [FR-003] Verify `ThemeAccent` enum structure supports theme-specific accents (each theme defines its own accent options)
- [ ] T002a [P] [FR-091, FR-098] Document JSON column structure in `specs/006-theming-engine/data-model.md` (required fields: theme, flavor, accent; flat structure; nullable column, explicitly state that no database migrations are required for this feature - uses existing `users.settings` column, define data migration scenarios if enum values change) per FR-091 and FR-098
- [ ] T002d [P] [FR-003] Create unit test `tests/Unit/Services/Theme/ThemeAccentMapperTest.php` to verify theme-specific accent validation, available accents per theme, and CSS variable name generation
- [ ] T002e [P] [FR-003, FR-093] Implement `ThemeAccentMapper` service in `app/Services/Theme/ThemeAccentMapper.php` with methods: `getAvailableAccents(Theme $theme): array`, `validateAccent(Theme $theme, ThemeAccent $accent): bool`, `getFluxVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string`, `getFilamentVariableName(Theme $theme, ThemeFlavor $flavor, ThemeAccent $accent): string` per FR-003 and plan.md section 5.4

-----

## 4. Phase 2: Foundation - Server-Side Injection

**Goal**: Implement the core mechanism to inject theme data into all views globally.
**Independent Test**: Page load contains `data-theme` attributes in `<html>` tag.

- [ ] T003 [FR-008, FR-009, FR-093] Create unit test `tests/Unit/Services/Theme/ThemeServiceTest.php` to verify validation logic (theme/flavor/accent matching, validation on every access, default accent resolution with "Primary" fallback to first available)
- [ ] T004 [FR-008, FR-009, FR-029, FR-047, FR-059, FR-092, FR-093, FR-094] Implement `ThemeService` in `app/Services/Theme/ThemeService.php` (handling validation on every access, default resolution with `ThemeAccent::Primary` default and fallback to first available accent if "Primary" doesn't exist, JSON size limit validation up to 64KB, enum serialization/deserialization validation per FR-092, integration with `ThemeAccentMapper` for accent validation, service failure handling: fallback to default theme with error logging when `ThemeAccentMapper` fails or throws exceptions per FR-047) and register it in `AppServiceProvider` (if not auto-discovered)
- [ ] T005 [FR-005] Create feature test `tests/Feature/Theme/ThemeGlobalApplicationTest.php` to assert View Composer injects `themeData` with all three attributes (`data-theme`, `data-flavor`, `data-accent`)
- [ ] T006 [FR-005, FR-094] Implement View Composer in `app/Providers/AppServiceProvider.php` to inject `ThemeData` into all views (`*`) using `ThemeService` and `ThemeAccentMapper` for validation
- [ ] T007 [FR-005, FR-006, FR-018] Update main application layout in `resources/views/components/layouts/app.blade.php` (or `resources/views/layouts/app.blade.php`) to apply `data-theme`, `data-flavor`, and `data-accent` attributes and `dark` class from `themeData`
- [ ] T008 [P] [FR-005, FR-051] Verify/Update Filament panel configuration to ensure it receives and renders `themeData` attributes (Investigate `renderHook('panels::body.start')` or Custom Layout approach in `app/Providers/Filament/AdminPanelProvider.php`)
- [ ] T008a [P] [FR-064] Verify Filament components maintain accessibility when themed (component focus states, ARIA attributes, keyboard navigation) per FR-064
- [ ] T009 [P] [FR-006] Create `resources/css/themes/all-themes.css` with all 15 theme definitions using attribute selectors `[data-theme="..."][data-flavor="..."][data-accent="..."]` and accent CSS variables (e.g., `--accent-flux-zinc-500`, `--accent-filament-gray-500`) per plan.md section 5.6
- [ ] T009c [P] [FR-006] Import `all-themes.css` in `resources/css/app.css` to ensure all theme definitions are available
- [ ] T009b [P] [FR-071] Verify CSS attribute selectors cannot be exploited: validate attribute values against allowed enum values before rendering per FR-071
- [ ] T009a [P] [FR-021] Create contrast validation test `tests/Feature/Theme/ThemeContrastTest.php` to verify all theme/flavor/accent combinations meet WCAG AA contrast requirements (4.5:1 for normal text, 3:1 for large text) per FR-021 (test all 15 themes and their combinations)

-----

## 5. Phase 3: User Story 1 - Personalize Application Appearance

**Goal**: Allow authenticated users to change themes with live preview and auto-save.
**Story**: [US1] Personalize Application Appearance
**Independent Test**: Changing settings in UI updates DB and DOM immediately.

- [ ] T010 [US1] [FR-004, FR-015, FR-016, FR-025, FR-026, FR-079] Create feature test `tests/Feature/Theme/ThemePersistenceTest.php` to verify user settings are saved to DB with database transactions, and verify "Reset to Default" button/control conditional visibility (hidden when at default, visible when changed) and functionality (resets to default theme) per FR-079
- [ ] T011 [P] [US1] [FR-006, FR-081] Create browser test `tests/Browser/Theme/LivePreviewTest.php` to verify DOM updates without reload
- [ ] T012 [US1] [FR-004, FR-007, FR-015, FR-016, FR-017, FR-019, FR-025, FR-026, FR-044, FR-046, FR-079, FR-123] Update `resources/views/livewire/settings/appearance.blade.php` (or Component class) to use `ThemeData` and `ThemeAccentMapper` service, implement reactive flavor/accent updates (flavor selector hidden when only one option exists, accent options update when theme changes), implement "Reset to Default" button/control with conditional visibility (displayed only when user's current theme selection differs from default theme, hidden when selection matches default per FR-079), and implement `updated()` auto-save logic with 300ms debounce (per FR-046), database transactions, 5 retries with exponential backoff (1s, 2s, 4s, 8s, 16s) with user feedback on failure (error message, retry mechanism, graceful degradation per FR-044), silent success feedback, validation error handling (prevent save, log error, notify user), and session expiration handling (complete save if user still authenticated, otherwise discard silently and require re-authentication on next interaction per FR-123)
- [ ] T012a [US1] [FR-045] Implement toast notifications for error/retry states in `appearance.blade.php` (error: "Theme update failed. Please try again.", retry: "Retrying theme update...", duration: 3 seconds, bottom-right positioning, ARIA live region, ESC dismissible) per FR-045
- [ ] T013 [US1] [FR-006, FR-081] Implement `$this->js()` calls in `appearance.blade.php` for immediate DOM updates (Live Preview) updating all three attributes (`data-theme`, `data-flavor`, `data-accent`) with silent auto-save feedback (no visual feedback on success) and 150ms CSS transitions (ease-out) per FR-081
- [ ] T013a [US1] [FR-079, FR-122] Ensure theme selection controls use radio buttons with horizontal layout (Theme → Flavor → Accent), clear labels, visual previews (color swatches), fieldset/legend grouping, and conditional rendering (flavor selector hidden when theme has only one flavor, accent selector updates reactively when theme changes) per FR-079 and clarifications
- [ ] T013b [US1] [FR-084] Ensure mobile responsiveness: touch targets minimum 44x44px, vertical stacking on small screens, adequate spacing per FR-084
- [ ] T013c [US1] [FR-054] Implement `prefers-reduced-motion` CSS media query handling: disable or reduce theme transitions when user prefers reduced motion (animation duration max 500ms, use ease-in-out easing) per FR-054
- [ ] T013d [US1] [FR-022] Verify full keyboard navigation for theme selection controls: Tab order, Enter/Space activation, focus management per FR-022
- [ ] T013e [US1] [FR-023] Verify ARIA labels for all theme selection controls and live region announcements for theme changes (e.g., "Theme changed to Catppuccin Mocha") per FR-023
- [ ] T013f [US1] [FR-024] Verify focus visibility when themes change dynamically: focus remains on control that triggered change, focus indicators with sufficient contrast visible in all theme combinations per FR-024
- [ ] T013g [US1] [FR-055] Verify theme information is not conveyed by color alone: theme names MUST be text labels, not just color swatches per FR-055
- [ ] T013h [US1] [FR-056] Verify error states, validation feedback, and success indicators remain visible and distinguishable in all theme combinations (sufficient contrast, non-color indicators) per FR-056
- [ ] T013i [US1] [FR-063] Verify clear, non-technical language for theme labels (e.g., "Dark Mode" vs "Mocha Flavor") with plain-language descriptions per FR-063
- [ ] T014 [US1] [FR-020] Implement rate limiting middleware for auto-save endpoint: sliding window (10 requests per 60 seconds per user) in `app/Http/Middleware/` or route middleware configuration
- [ ] T015 [US1] [FR-006, FR-035] Update `resources/js/app.js` to ensure `initializeTheme()` reads existing server-side attributes first (DO NOT overwrite if present) and handles `dark` class toggling correctly on load/change
- [ ] T015a [US1] [FR-095] Create feature test `tests/Feature/Theme/ThemeAutoSaveStrategyTest.php` to verify debounced auto-save strategy (300ms delay after last property change), verify 5 retries with exponential backoff (delays: 1s, 2s, 4s, 8s, 16s) for database save failures, and verify auto-save trigger behavior is consistent across all theme preference changes per FR-095
- [ ] T015b [US1] [FR-096] Create feature test `tests/Feature/Theme/ThemeStateConsistencyTest.php` to verify data consistency between database and in-memory state (User model, Livewire component, View Composer), verify state synchronization when theme changes occur (all components see updated state immediately), and verify data consistency when user settings are updated via multiple paths (Livewire component, direct model update, migration) per FR-096
- [ ] T015c [US1] [FR-097] Create feature test `tests/Feature/Theme/ThemeValidationErrorHandlingTest.php` to verify validation failures during save prevent save, log error, and notify user with user-friendly error message, verify validation failures are handled consistently, and verify validation rules are consistent across all entry points (Livewire component, View Composer, direct model updates) per FR-097
- [ ] T015d [US1] [FR-082] Add test assertion to `tests/Feature/Theme/ThemePersistenceTest.php` to verify silent auto-save feedback (no visual feedback when auto-save succeeds) and verify UI clearly communicates that changes are saved automatically (no "Save" button visible or button disabled/removed) per FR-082

-----

## 6. Phase 4: User Story 2 - System Default Fallback & Validation

**Goal**: Ensure invalid states are auto-corrected silently.
**Story**: [US2] System Default Fallback

- [ ] T016 [US2] [FR-009, FR-027, FR-043, FR-047] Create feature test `tests/Feature/Theme/ThemeValidationTest.php` to verify invalid DB states (invalid theme/flavor/accent combinations) are auto-corrected on every access (not just on load) with default accent fallback to "Primary" or first available, verify `ThemeAccentMapper` service failure handling (fallback to default theme with error logging when service fails or throws exceptions) per FR-047, and include tests for edge cases: invalid theme combinations, corrupted data, concurrent updates, null/empty states, rapid successive changes per FR-043
- [ ] T017 [US2] [FR-009, FR-027] Update `App\Models\User::booted()` or `ThemeService` integration to ensure `UserSettingsData` is validated and corrected on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.) using `ThemeAccentMapper` for accent validation
- [ ] T017a [US2] [FR-092] Implement enum deserialization failure handling in `UserSettingsData::from()` to catch invalid enum values and trigger validation/correction per FR-092, including theme-specific accent validation via `ThemeAccentMapper`
- [ ] T018 [US2] [FR-028] Verify silent persistence of corrected settings in `ThemeService` or `User` model after validation correction
- [ ] T018a [US2] [FR-030] Implement account deletion cleanup: add `User::deleting()` event handler to clean up theme preferences when user account is deleted per FR-030
- [ ] T018b [US1] [FR-123] Create feature test `tests/Feature/Theme/ThemeSessionExpirationTest.php` to verify session expiration during auto-save: auto-save completes if user still authenticated after session expiration, auto-save discards silently if authentication expired, re-authentication required on next interaction per FR-123
- [ ] T018c [US2] [FR-047] Create feature test `tests/Feature/Theme/ThemeServiceFailureTest.php` to verify `ThemeAccentMapper` service failure fallback behavior (fallback to default theme when service fails or throws exceptions), error logging when service fails, and graceful degradation (no user-facing errors) per FR-047

-----

## 7. Phase 5: User Story 3 - Public Theme Preview

**Goal**: Public demo page with session-based theming.
**Story**: [US3] Public Theme Preview

- [ ] T019 [US3] [FR-010, FR-011, FR-012] Create feature test `tests/Feature/ThemePreview/ThemePreviewPageTest.php` (verify public access, all 15 themes accessible, session storage isolation, theme changes reset on navigation)
- [ ] T020 [US3] [FR-010] Create `resources/views/pages/themes/preview.blade.php` (Folio route: `/themes/preview`) with theme switching controls for all 15 themes organized by category (Global Developer Themes and UK Authentic Design System Themes)
- [ ] T020a [US3] [FR-080] Add "Preview Mode" banner to `preview.blade.php` with messaging: "Preview Mode - Changes are temporary and will reset when you leave this page" per FR-080
- [ ] T020b [US3] [FR-007, FR-011] Implement reactive flavor/accent selectors in `preview.blade.php` using `ThemeAccentMapper` service (flavor selector hidden when only one option exists, accent options update when theme changes, accent defaults to "Primary" or first available)
- [ ] T021 [US3] [FR-011, FR-060] Implement session storage logic in `resources/views/pages/themes/preview.blade.php` (or linked JS) to handle temporary theme application for all three attributes (`data-theme`, `data-flavor`, `data-accent`)
- [ ] T022 [US3] [FR-011, FR-012] Ensure `preview.blade.php` uses `themeData` defaults if session is empty, and updates DOM on selection with all three attributes
- [ ] T022a [US3] [FR-080] Add public link to preview page in footer or public navigation ("Try Themes" or "Theme Preview") per FR-080
- [ ] T022b [US3] [FR-119] Document preview page performance requirements: define preview page load performance requirements (initial load time, theme switching latency), preview page performance requirements when using session storage (sessionStorage read/write overhead), preview page performance requirements under different network conditions (slow network, offline mode), and preview page performance consistency requirements (should performance match authenticated settings page) per FR-119
- [ ] T022c [US3] [FR-120] Document preview page performance acceptance criteria: define performance acceptance criteria for different operations (theme change, page load, validation), performance acceptance criteria under different conditions (normal load, high load), and performance regression acceptance requirements (how much performance degradation is acceptable, thresholds) per FR-120

-----

## 8. Phase 6: Polish & Performance

**Goal**: Ensure performance targets are met and code is clean.

- [ ] T023 [FR-032, FR-033, FR-034, FR-042, FR-109, SC-002] Create performance test `tests/Feature/Theme/ThemePerformanceTest.php` (Implement performance markers/telemetry to assert p95 latency < 200ms from user click to visual feedback completion, measure p50, p95, p99, max percentiles per FR-032, verify same performance target p95 < 200ms for all load conditions - normal load, high load, network latency scenarios per FR-034, explicitly assert p95 < 200ms target is met per FR-042, scope performance targets to client-side DOM updates only - measure only time from user click to visual feedback per FR-109)
- [ ] T023a [FR-110, FR-035] Create performance test for initial page load: TTFP < 1s, TTI < 2s, attributes set within 50ms per FR-110
- [ ] T023b [FR-111] Document database performance requirements: define database write performance requirements during auto-save (latency, throughput, acceptable overhead), database query performance requirements when reading user settings (query time, caching requirements), database performance requirements under concurrent theme updates (multiple tabs, simultaneous saves), and database performance requirements when validation occurs (validation overhead, correction persistence time) per FR-111
- [ ] T023c [FR-112] Document client-side performance requirements: define JavaScript execution performance requirements (DOM update time, attribute setting overhead), CSS application performance requirements (attribute selector matching time, style recalculation overhead), and browser rendering performance requirements (repaint time, reflow prevention, layout shift avoidance) per FR-112
- [ ] T023d [FR-113] Document device and browser performance requirements: define client-side performance targets for different devices (mobile, tablet, desktop performance targets) and client-side performance requirements for different browsers (Chrome, Firefox, Safari compatibility and performance) per FR-113
- [ ] T023e [FR-114] Document scalability and network performance requirements: define network bandwidth usage requirements (CSS file sizes, JavaScript bundle sizes, asset loading), scalability requirements (performance under high user load, concurrent theme changes), resource usage requirements when adding new themes (should performance degrade, acceptable overhead), and performance requirements under concurrent user load (multiple users changing themes simultaneously) per FR-114
- [ ] T024 [FR-013] Run `php artisan test` and ensure all tests pass
- [ ] T024d [FR-040] Verify test coverage meets minimum 90% requirement: run `php artisan test --coverage` or use Pest coverage tools to measure coverage for all theme-related code, ensure coverage is measured and enforced (add to CI/CD pipeline if applicable) per FR-040
- [ ] T024a [FR-031, FR-059, FR-061, FR-075] Create security test `tests/Feature/Theme/ThemeSecurityTest.php` (XSS testing, CSRF verification, input validation, dependency scanning, theme-specific accent validation security, verify no hardcoded secrets in theme-related code or configuration files per FR-061) per FR-075
- [ ] T024c [FR-003, FR-047] Create feature test `tests/Feature/Theme/ThemeAccentMapperTest.php` to verify theme-specific accent validation, available accents per theme queries, CSS variable name generation, and service failure handling (fallback to default theme with error logging) per plan.md section 5.4 and FR-047
- [ ] T024b [FR-062, FR-066] Create accessibility test `tests/Feature/Theme/ThemeAccessibilityTest.php` (automated axe-core testing, keyboard navigation verification, screen reader testing with NVDA/JAWS/VoiceOver, ARIA label verification, focus management, verify theme data attributes do not interfere with assistive technology parsing - data attributes used only for styling not semantic meaning per FR-062) per FR-066
- [ ] T025 [FR-005, FR-051] Manual verification of Filament Admin Panel theming
- [ ] T025b [FR-041] Create automated integration test `tests/Feature/Theme/ThemeFilamentIntegrationTest.php` to verify theme data attributes are present in Filament panel views (admin dashboard, resource pages, etc.) and verify themes apply correctly in Filament components per FR-041
- [ ] T025a [FR-069] Verify default theme (Catppuccin Mocha) meets accessibility standards out of the box (WCAG AA contrast, keyboard navigation, screen reader support) per FR-069
- [ ] T026 [FR-005, FR-051] Manual verification of Auth pages (Login/Register) theming
- [ ] T026d [FR-041] Create automated integration test `tests/Feature/Theme/ThemeFortifyIntegrationTest.php` to verify theme data attributes are present in Fortify authentication pages (login, register, password reset, etc.) and verify themes apply correctly in auth page components per FR-041
- [ ] T026a [FR-065] Verify authentication pages (Fortify) remain accessible when themed (contrast requirements met, focus indicators visible, form labels readable) per FR-065
- [ ] T026b [FR-070] Verify graceful degradation when CSS or JavaScript fails: theme still readable, no broken layouts, content remains accessible per FR-070
- [ ] T026c [FR-074] Verify session fixation prevention: session regeneration occurs on authentication when transitioning from preview page to authenticated state per FR-074
- [ ] T027 [FR-014] Configure Laravel Telescope & Horizon, emitting structured logs/metrics for theme validation corrections and preview interactions
- [ ] T027a [FR-036] Implement Telescope event recording in `ThemeService` and Livewire component: record `theme_changed`, `validation_corrected`, `preview_interaction` events with required fields (event_type, timestamp, timezone) and optional fields (user_id, session_id, request_id, performance metrics) per FR-036
- [ ] T027b [FR-038] Configure log levels in `config/logging.php`: info (theme changes, preview interactions), warning (validation corrections, retries), error (save failures, deserialization failures), debug (performance markers) per FR-038
- [ ] T027b1 [FR-104] Define error context structure in logs: ensure error logs include sufficient context for debugging (stack traces, request context, user context) without exposing sensitive data, define error alerting requirements (when to alert, severity levels, notification channels), and implement error rate tracking (error frequency, error types, resolution tracking) per FR-104
- [ ] T027c [FR-039] Configure Telescope data retention: set `TELESCOPE_DB_RETENTION_DAYS=7` in `.env` per FR-039
- [ ] T027d [FR-099] Create Telescope custom views for theme events (filter by `event_type = 'theme_changed'`) and configure dashboard metrics (p50, p95, p99 latencies, event counts, error rates) per FR-099
- [ ] T027e [FR-101] Implement performance instrumentation: use browser Performance API and `Telescope::recordPerformance()` to record p50, p95, p99, max percentiles, DOM update time, database query time, total time per FR-101
- [ ] T027f [FR-102] Implement invalid theme combination tracking: log what was invalid, what was corrected to, correction frequency per FR-102
- [ ] T027g [FR-103] Implement preview page interaction tracking: track theme changes, navigation, usage patterns, conversions, performance per FR-103
- [ ] T027h [FR-105] Configure observability dashboards: define dashboards needed, metrics displayed, real-time vs. historical, access control per FR-105
- [ ] T027i [FR-106] Define alert conditions: when to alert, thresholds, conditions, alert channels, severity levels, deduplication per FR-106
- [ ] T027j [FR-107] Create observability testing: verify events are captured, metrics recorded, acceptance criteria met, regression testing per FR-107
- [ ] T027k [FR-037] Verify telemetry anonymization: PII exclusion, data masking rules, GDPR compliance, sensitive data exclusion per FR-037
- [ ] T027l [FR-077] Implement security audit logging: log failed validations, unauthorized access attempts, rate limit violations, theme preference changes (user id, timestamp, previous value, new value, source IP) per FR-077
- [ ] T027m [FR-100] Configure Horizon for theme operations: define when Horizon should be configured (if queues are used for theme operations), what queue metrics are relevant for theming, when queues are used, Horizon dashboard setup requirements (what dashboards, what metrics displayed), and requirements for handling Horizon when no queues are used (is Horizon optional, should it be disabled) per FR-100
- [ ] T027n [FR-108] Configure Telescope setup and installation: install Telescope package, configure installation steps, define environment-specific configuration requirements (development, staging, production - same or different configs), define observability feature flag requirements (can observability be disabled, environment-specific toggles), and define observability performance overhead requirements (acceptable impact on application performance, resource usage) per FR-108
- [ ] T028 [FR-010] Document shared asset strategy (README/runbook update) and verify `/themes/preview` loads production bundles with short cache headers
- [ ] T028a [FR-052] Document database indexing decision in `specs/006-theming-engine/data-model.md` (no indexing required, rationale: user ID already indexed) per FR-052
- [ ] T028b [FR-067] Create accessibility documentation in `docs/accessibility.md` or `specs/006-theming-engine/accessibility.md`: document accessibility features and limitations for each theme combination (contrast ratios, keyboard navigation support, screen reader compatibility) per FR-067
- [ ] T028c [FR-053] Document backward compatibility strategy for future `users.settings` schema changes (migration path, data transformation, rollback procedures) per FR-053
- [ ] T028d [FR-061, FR-076] Document security acceptance criteria: all inputs validated, all outputs encoded, no XSS vulnerabilities, CSRF protection verified per FR-076
- [ ] T028e [FR-115] Document performance degradation scenarios: what happens when system is under stress, graceful degradation requirements per FR-115
- [ ] T028f [FR-118] Document performance optimization guidelines: when to optimize, acceptable trade-offs, caching requirements, lazy loading, code splitting, optimization priorities per FR-118
- [ ] T028g [FR-068] Verify error messages are accessible when theme validation fails: screen reader announcements via live regions, visible text, sufficient contrast per FR-068
- [ ] T028m [FR-116] Document performance testing methodology: define performance testing methodology requirements (load testing, stress testing, benchmark testing), performance test scenario requirements (what scenarios must be tested - normal load, high load, edge cases), and performance test environment requirements (should tests run in production-like environments) per FR-116
- [ ] T028o [FR-117] Document performance monitoring implementation: define performance monitoring implementation requirements (what tools, what metrics, how often), performance data collection and storage requirements (where performance data is stored, retention policies), performance dashboard requirements (what dashboards, what metrics displayed, real-time vs. historical), and performance alerting requirements (when to alert on performance degradation, thresholds, notification channels) per FR-117
- [ ] T028h [FR-073] Verify user settings data (theme preferences) are not exposed in application logs or error messages: logging disabled or anonymized, validation failures logged securely per FR-073
- [ ] T028i [FR-072] Verify JavaScript updates to DOM attributes are safe: no eval, no innerHTML manipulation, use safe DOM methods like `setAttribute` or `dataset` per FR-072
- [ ] T028j [FR-058] Document resource exhaustion limits and handling: memory limits, CPU limits, database connection limits, JSON payload size limits per FR-058
- [ ] T028k [FR-049] Create dependency update task: keep theme-related dependencies (Livewire, Flux, Filament) up-to-date with security patches, test for compatibility per FR-049
- [ ] T028l [FR-050] Create vulnerability scanning task: run `composer audit` and `npm audit` regularly, address security vulnerabilities per FR-050
- [ ] T028p [FR-048] Create theme extension documentation in `docs/theming-extension.md` or `specs/006-theming-engine/extension-guide.md`: document theme extension points including enum extension procedures, CSS file updates, ThemeAccentMapper service updates, validation rule updates, and migration strategy for existing user data per FR-048
- [ ] T028q [FR-083] Document loading state implementation decision in `specs/006-theming-engine/plan.md` section 5.8 or implementation notes: specify when to use skeleton vs spinner vs immediate render for theme preference fetching, ensure loading states prevent layout shift and provide user feedback per FR-083
- [ ] T028r [FR-090] Document UX success metrics measurement methodology in `specs/006-theming-engine/plan.md` or create `docs/ux-metrics.md`: define how to measure user satisfaction (surveys, feedback forms), error rates (tracking system), and task completion time (analytics) per FR-090
- [ ] T028s [FR-078, FR-085, FR-086, FR-087, FR-088, FR-089] Add visual regression test task or design review checklist: verify visual design consistency (color schemes, typography, spacing, visual hierarchy), verify visual hierarchy and layout requirements for theme selection controls, verify state transition requirements (smooth color transitions), verify theme appearance consistency between Filament/Fortify and main application, verify perceived performance requirements, verify UX requirements for edge cases per FR-078, FR-085, FR-086, FR-087, FR-088, FR-089 (or accept as design guidance implemented implicitly in T013a, T013b, T025, T026)

-----
