# Tasks: Theming Engine

**Feature**: Theming Engine
**Status**: Draft
**Spec**: [spec.md](./spec.md)
**Plan**: [plan.md](./plan.md)

## Dependencies

- **Phase 1 (Setup)**: Blocks Phase 2.
- **Phase 2 (Foundation)**: Blocks Phase 3, 4, 5. (Server-side injection is the core mechanism).
- **Phase 3 (US1)**: Independent of US2, US3.
- **Phase 4 (US2)**: Depends on Phase 2 (Validation logic).
- **Phase 5 (US3)**: Depends on Phase 2 (Theme structure).

## Implementation Strategy

- **TDD Enforced**: All implementation tasks must be preceded by a corresponding test task.
- **Hybrid Approach**: Implement server-side injection first (Foundation), then client-side reactivity (US1), then public preview (US3).
- **MVP**: Phases 1, 2, and 3 constitute the MVP.

---

## Phase 1: Setup & Data Structures

**Goal**: Initialize core data structures required for theming.

- [ ] T001 [P] Create `ThemeData` DTO with properties and `isLight()` method in `app/Data/ThemeData.php`
- [ ] T002 [P] Verify existing Enums (`Theme`, `ThemeFlavor`, `ThemeAccent`) in `app/Enums/` and ensure `ThemeFlavor::isLight()` exists
- [ ] T002a [P] Document JSON column structure in `specs/006-theming-engine/data-model.md` (required fields: theme, flavor, accent; flat structure; nullable column) per FR-091

## Phase 2: Foundation - Server-Side Injection

**Goal**: Implement the core mechanism to inject theme data into all views globally.
**Independent Test**: Page load contains `data-theme` attributes in `<html>` tag.

- [ ] T003 Create unit test `tests/Unit/Services/Theme/ThemeServiceTest.php` to verify validation logic (theme/flavor matching, validation on every access)
- [ ] T004 Implement `ThemeService` in `app/Services/Theme/ThemeService.php` (handling validation on every access, default resolution, JSON size limit validation up to 64KB, enum serialization/deserialization validation per FR-092) and register it in `AppServiceProvider` (if not auto-discovered)
- [ ] T005 Create feature test `tests/Feature/Theme/ThemeGlobalApplicationTest.php` to assert View Composer injects `themeData`
- [ ] T006 Implement View Composer in `app/Providers/AppServiceProvider.php` to inject `ThemeData` into all views (`*`)
- [ ] T007 Update main application layout in `resources/views/components/layouts/app.blade.php` (or `resources/views/layouts/app.blade.php`) to apply `data-*` attributes and `dark` class from `themeData`
- [ ] T008 [P] Verify/Update Filament panel configuration to ensure it receives and renders `themeData` attributes (Investigate `renderHook('panels::body.start')` or Custom Layout approach in `app/Providers/Filament/AdminPanelProvider.php`)
- [ ] T008a [P] Verify Filament components maintain accessibility when themed (component focus states, ARIA attributes, keyboard navigation) per FR-064
- [ ] T009 [P] Verify/Update CSS in `resources/css/app.css` to ensure attribute selectors `[data-theme="..."]` are defined (as per plan)
- [ ] T009b [P] Verify CSS attribute selectors cannot be exploited: validate attribute values against allowed enum values before rendering per FR-071
- [ ] T009a [P] Create contrast validation test `tests/Feature/Theme/ThemeContrastTest.php` to verify all theme/flavor/accent combinations meet WCAG AA contrast requirements (4.5:1 for normal text, 3:1 for large text) per FR-021

## Phase 3: User Story 1 - Personalize Application Appearance

**Goal**: Allow authenticated users to change themes with live preview and auto-save.
**Story**: [US1] Personalize Application Appearance
**Independent Test**: Changing settings in UI updates DB and DOM immediately.

- [ ] T010 [US1] Create feature test `tests/Feature/Theme/ThemePersistenceTest.php` to verify user settings are saved to DB with database transactions
- [ ] T011 [P] [US1] Create browser test `tests/Browser/Theme/LivePreviewTest.php` to verify DOM updates without reload
- [ ] T012 [US1] Update `resources/views/livewire/settings/appearance.blade.php` (or Component class) to use `ThemeData` and implement `updated()` auto-save logic with 300ms debounce, database transactions, 5 retries with exponential backoff (1s, 2s, 4s, 8s, 16s), silent success feedback, and validation error handling (prevent save, log error, notify user)
- [ ] T012a [US1] Implement toast notifications for error/retry states in `appearance.blade.php` (error: "Theme update failed. Please try again.", retry: "Retrying theme update...", duration: 3 seconds, bottom-right positioning, ARIA live region, ESC dismissible) per FR-045
- [ ] T013 [US1] Implement `$this->js()` calls in `appearance.blade.php` for immediate DOM updates (Live Preview) with silent auto-save feedback (no visual feedback on success) and 150ms CSS transitions (ease-out) per FR-081
- [ ] T013a [US1] Ensure theme selection controls use radio buttons with horizontal layout (Theme → Flavor → Accent), clear labels, visual previews (color swatches), and fieldset/legend grouping per FR-079
- [ ] T013b [US1] Ensure mobile responsiveness: touch targets minimum 44x44px, vertical stacking on small screens, adequate spacing per FR-084
- [ ] T013c [US1] Implement `prefers-reduced-motion` CSS media query handling: disable or reduce theme transitions when user prefers reduced motion (animation duration max 500ms, use ease-in-out easing) per FR-054
- [ ] T013d [US1] Verify full keyboard navigation for theme selection controls: Tab order, Enter/Space activation, focus management per FR-022
- [ ] T013e [US1] Verify ARIA labels for all theme selection controls and live region announcements for theme changes (e.g., "Theme changed to Catppuccin Mocha") per FR-023
- [ ] T013f [US1] Verify focus visibility when themes change dynamically: focus remains on control that triggered change, focus indicators with sufficient contrast visible in all theme combinations per FR-024
- [ ] T013g [US1] Verify theme information is not conveyed by color alone: theme names MUST be text labels, not just color swatches per FR-055
- [ ] T013h [US1] Verify error states, validation feedback, and success indicators remain visible and distinguishable in all theme combinations (sufficient contrast, non-color indicators) per FR-056
- [ ] T013i [US1] Verify clear, non-technical language for theme labels (e.g., "Dark Mode" vs "Mocha Flavor") with plain-language descriptions per FR-063
- [ ] T014 [US1] Implement rate limiting middleware for auto-save endpoint: sliding window (10 requests per 60 seconds per user) in `app/Http/Middleware/` or route middleware configuration
- [ ] T015 [US1] Update `resources/js/app.js` to ensure `initializeTheme()` reads existing server-side attributes first (DO NOT overwrite if present) and handles `dark` class toggling correctly on load/change

## Phase 4: User Story 2 - System Default Fallback & Validation

**Goal**: Ensure invalid states are auto-corrected silently.
**Story**: [US2] System Default Fallback

- [ ] T016 [US2] Create feature test `tests/Feature/Theme/ThemeValidationTest.php` to verify invalid DB states are auto-corrected on every access (not just on load)
- [ ] T017 [US2] Update `App\Models\User::booted()` or `ThemeService` integration to ensure `UserSettingsData` is validated and corrected on every access (whenever settings are read: View Composer, Livewire mount, direct model access, etc.)
- [ ] T017a [US2] Implement enum deserialization failure handling in `UserSettingsData::from()` to catch invalid enum values and trigger validation/correction per FR-092
- [ ] T018 [US2] Verify silent persistence of corrected settings in `ThemeService` or `User` model after validation correction
- [ ] T018a [US2] Implement account deletion cleanup: add `User::deleting()` event handler to clean up theme preferences when user account is deleted per FR-030

## Phase 5: User Story 3 - Public Theme Preview

**Goal**: Public demo page with session-based theming.
**Story**: [US3] Public Theme Preview

- [ ] T019 [US3] Create feature test `tests/Feature/ThemePreview/ThemePreviewPageTest.php` (verify public access, session storage isolation)
- [ ] T020 [US3] Rename/Move `resources/views/pages/tailwindcss.catppuccin.com/index.blade.php` to `resources/views/pages/themes/preview.blade.php`
- [ ] T020a [US3] Add "Preview Mode" banner to `preview.blade.php` with messaging: "Preview Mode - Changes are temporary and will reset when you leave this page" per FR-080
- [ ] T021 [US3] Implement session storage logic in `resources/views/pages/themes/preview.blade.php` (or linked JS) to handle temporary theme application
- [ ] T022 [US3] Ensure `preview.blade.php` uses `themeData` defaults if session is empty, and updates DOM on selection
- [ ] T022a [US3] Add public link to preview page in footer or public navigation ("Try Themes" or "Theme Preview") per FR-080

## Phase 6: Polish & Performance

**Goal**: Ensure performance targets are met and code is clean.

- [ ] T023 Create performance test `tests/Feature/Theme/ThemePerformanceTest.php` (Implement performance markers/telemetry to assert p95 latency < 200ms from user click to visual feedback completion, measure p50, p95, p99, max percentiles per FR-032)
- [ ] T023a Create performance test for initial page load: TTFP < 1s, TTI < 2s, attributes set within 50ms per FR-110
- [ ] T024 Run `php artisan test` and ensure all tests pass
- [ ] T024a Create security test `tests/Feature/Theme/ThemeSecurityTest.php` (XSS testing, CSRF verification, input validation, dependency scanning) per FR-075
- [ ] T024b Create accessibility test `tests/Feature/Theme/ThemeAccessibilityTest.php` (automated axe-core testing, keyboard navigation verification, screen reader testing with NVDA/JAWS/VoiceOver, ARIA label verification, focus management) per FR-066
- [ ] T025 Manual verification of Filament Admin Panel theming
- [ ] T025a Verify default theme (Catppuccin Mocha) meets accessibility standards out of the box (WCAG AA contrast, keyboard navigation, screen reader support) per FR-069
- [ ] T026 Manual verification of Auth pages (Login/Register) theming
- [ ] T026a Verify authentication pages (Fortify) remain accessible when themed (contrast requirements met, focus indicators visible, form labels readable) per FR-065
- [ ] T026b Verify graceful degradation when CSS or JavaScript fails: theme still readable, no broken layouts, content remains accessible per FR-070
- [ ] T026c Verify session fixation prevention: session regeneration occurs on authentication when transitioning from preview page to authenticated state per FR-074
- [ ] T027 Configure Laravel Telescope & Horizon, emitting structured logs/metrics for theme validation corrections and preview interactions
- [ ] T027a Implement Telescope event recording in `ThemeService` and Livewire component: record `theme_changed`, `validation_corrected`, `preview_interaction` events with required fields (event_type, timestamp, timezone) and optional fields (user_id, session_id, request_id, performance metrics) per FR-036
- [ ] T027b Configure log levels in `config/logging.php`: info (theme changes, preview interactions), warning (validation corrections, retries), error (save failures, deserialization failures), debug (performance markers) per FR-038
- [ ] T027c Configure Telescope data retention: set `TELESCOPE_DB_RETENTION_DAYS=7` in `.env` per FR-039
- [ ] T027d Create Telescope custom views for theme events (filter by `event_type = 'theme_changed'`) and configure dashboard metrics (p50, p95, p99 latencies, event counts, error rates) per FR-099
- [ ] T027e Implement performance instrumentation: use browser Performance API and `Telescope::recordPerformance()` to record p50, p95, p99, max percentiles, DOM update time, database query time, total time per FR-101
- [ ] T027f Implement invalid theme combination tracking: log what was invalid, what was corrected to, correction frequency per FR-102
- [ ] T027g Implement preview page interaction tracking: track theme changes, navigation, usage patterns, conversions, performance per FR-103
- [ ] T027h Configure observability dashboards: define dashboards needed, metrics displayed, real-time vs. historical, access control per FR-105
- [ ] T027i Define alert conditions: when to alert, thresholds, conditions, alert channels, severity levels, deduplication per FR-106
- [ ] T027j Create observability testing: verify events are captured, metrics recorded, acceptance criteria met, regression testing per FR-107
- [ ] T027k Verify telemetry anonymization: PII exclusion, data masking rules, GDPR compliance, sensitive data exclusion per FR-037
- [ ] T027l Implement security audit logging: log failed validations, unauthorized access attempts, rate limit violations, theme preference changes (user id, timestamp, previous value, new value, source IP) per FR-077
- [ ] T028 Document shared asset strategy (README/runbook update) and verify `/themes/preview` loads production bundles with short cache headers
- [ ] T028a Document database indexing decision in `specs/006-theming-engine/data-model.md` (no indexing required, rationale: user ID already indexed) per FR-052
- [ ] T028b Create accessibility documentation in `docs/accessibility.md` or `specs/006-theming-engine/accessibility.md`: document accessibility features and limitations for each theme combination (contrast ratios, keyboard navigation support, screen reader compatibility) per FR-067
- [ ] T028c Document backward compatibility strategy for future `users.settings` schema changes (migration path, data transformation, rollback procedures) per FR-053
- [ ] T028d Document security acceptance criteria: all inputs validated, all outputs encoded, no XSS vulnerabilities, CSRF protection verified per FR-076
- [ ] T028e Document performance degradation scenarios: what happens when system is under stress, graceful degradation requirements per FR-115
- [ ] T028f Document performance optimization guidelines: when to optimize, acceptable trade-offs, caching requirements, lazy loading, code splitting, optimization priorities per FR-118
- [ ] T028g Verify error messages are accessible when theme validation fails: screen reader announcements via live regions, visible text, sufficient contrast per FR-068
- [ ] T028h Verify user settings data (theme preferences) are not exposed in application logs or error messages: logging disabled or anonymized, validation failures logged securely per FR-073
- [ ] T028i Verify JavaScript updates to DOM attributes are safe: no eval, no innerHTML manipulation, use safe DOM methods like `setAttribute` or `dataset` per FR-072
- [ ] T028j Document resource exhaustion limits and handling: memory limits, CPU limits, database connection limits, JSON payload size limits per FR-058
- [ ] T028k Create dependency update task: keep theme-related dependencies (Livewire, Flux, Filament) up-to-date with security patches, test for compatibility per FR-049
- [ ] T028l Create vulnerability scanning task: run `composer audit` and `npm audit` regularly, address security vulnerabilities per FR-050
