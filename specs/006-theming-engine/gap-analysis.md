# Gap Analysis: Missing Requirements from Checklists

**Created**: 2025-11-25
**Purpose**: Identify missing requirements flagged by checklists that should be added to `spec.md`

## Missing Requirements Table

| Missing Checklist Item | Proposed Requirement Text | Confidence Score (1-5) |
| :--- | :--- | :--- |
| **Authentication & Authorization** | **FR-015**: System MUST require authentication for the Livewire appearance settings component. Unauthenticated access attempts MUST be handled with redirect to login or 403 Forbidden response. | 5 |
| **Authorization - User Isolation** | **FR-016**: System MUST ensure users can only modify their own theme settings. Cross-user access MUST be prevented through proper authorization checks. | 5 |
| **Input Validation Before Persistence** | **FR-017**: System MUST validate theme/flavor/accent combinations and enum values BEFORE database persistence (not just on load). Invalid data MUST be rejected at the input boundary. | 5 |
| **XSS Prevention** | **FR-018**: System MUST explicitly encode all theme data attributes (`data-theme`, `data-flavor`, `data-accent`) to prevent XSS attacks. Laravel's automatic escaping MUST be verified and documented. | 5 |
| **CSRF Protection** | **FR-019**: System MUST explicitly require CSRF token validation on all theme preference update requests. Livewire's automatic CSRF handling MUST be verified and documented. | 5 |
| **Rate Limiting** | **FR-020**: System MUST implement rate limiting on the auto-save endpoint to prevent abuse from rapid successive saves (DoS prevention). | 4 |
| **WCAG Contrast Requirements** | **FR-021**: System MUST ensure all theme/flavor/accent combinations meet WCAG AA contrast requirements (4.5:1 for normal text, 3:1 for large text). All combinations MUST be validated for contrast compliance. | 5 |
| **Keyboard Navigation** | **FR-022**: System MUST provide full keyboard navigation for all theme selection controls (Tab order, Enter/Space activation, focus management). | 5 |
| **Screen Reader Support** | **FR-023**: System MUST provide ARIA labels for all theme selection controls and live region announcements for theme changes (e.g., "Theme changed to Catppuccin Mocha"). | 5 |
| **Focus Management** | **FR-024**: System MUST maintain focus visibility when themes change dynamically (live preview). Focus MUST remain on the control that triggered the change, and focus indicators MUST be visible in all themes. | 4 |
| **Database Transaction Handling** | **FR-025**: System MUST use database transactions for auto-save operations to ensure atomicity (all-or-nothing). Failed saves MUST rollback completely. | 4 |
| **Concurrent Update Handling** | **FR-026**: System MUST define behavior for concurrent theme updates (user changes theme in multiple tabs). Strategy MUST be specified: last write wins, merge, or conflict resolution. | 4 |
| **Data Corruption Detection** | **FR-027**: System MUST detect and handle corrupted database data beyond invalid enum values (malformed JSON, invalid structure, type mismatches, missing fields). | 4 |
| **Validation Persistence** | **FR-028**: System MUST persist corrected theme settings back to database after validation correction to prevent repeated corrections on every load. | 4 |
| **JSON Column Size Limits** | **FR-029**: System MUST define maximum JSON column size limits for `users.settings` and handle oversized payloads appropriately (reject or truncate). | 3 |
| **Data Retention Policy** | **FR-030**: System MUST define data retention policy for user theme preferences (how long stored, deletion on account removal, archival strategy). | 3 |
| **Error Message Security** | **FR-031**: System MUST ensure error messages do not leak sensitive information (database structure, enum values, internal paths, stack traces). Error messages MUST be user-friendly and non-technical. | 4 |
| **Performance - Additional Percentiles** | **FR-032**: System MUST define performance targets for additional percentiles beyond p95 (p50, p99, max) for comprehensive performance measurement. | 3 |
| **Performance - Measurement Points** | **FR-033**: System MUST define exact performance measurement points (when latency is measured: user click, DOM update completion, visual feedback). | 4 |
| **Performance - Load Conditions** | **FR-034**: System MUST define performance targets under different load conditions (normal load, high load, network latency scenarios). | 3 |
| **Performance - FOUC Prevention** | **FR-035**: System MUST prevent Flash of Unstyled Content (FOUC) by ensuring theme data attributes are present in HTML before CSS applies. Performance requirement: attributes MUST be set within [X]ms of page load. | 4 |
| **Observability - Event Structure** | **FR-036**: System MUST define explicit event data structure for telemetry (required fields, optional fields, field types, format). | 4 |
| **Observability - Privacy Safeguards** | **FR-037**: System MUST anonymize user data in telemetry (PII exclusion, data masking rules). Telemetry MUST comply with GDPR/privacy requirements (user consent, right to deletion). | 4 |
| **Observability - Log Levels** | **FR-038**: System MUST define log level requirements (info, warning, error, debug) for different theme events and error conditions. | 3 |
| **Observability - Data Retention** | **FR-039**: System MUST define data retention policies for observability data (how long logs/metrics are kept, archival policies, deletion schedules). | 3 |
| **Test Coverage Threshold** | **FR-040**: System MUST achieve minimum 90% test coverage for all theme-related code. Coverage MUST be measured and enforced. | 4 |
| **Test - Integration Requirements** | **FR-041**: System MUST include integration tests for theme application to Filament panels and Fortify authentication pages. Tests MUST verify theme data attributes are present and themes apply correctly. | 4 |
| **Test - Performance Requirements** | **FR-042**: System MUST include performance tests that measure p95 latency for theme changes and verify p95 < 200ms target is met. | 4 |
| **Test - Edge Cases** | **FR-043**: System MUST include tests for edge cases: invalid theme combinations, corrupted data, concurrent updates, null/empty states, rapid successive changes. | 4 |
| **UX - Auto-Save Failure Handling** | **FR-044**: System MUST define user feedback when auto-save fails (error message, retry mechanism, graceful degradation). Users MUST be notified of save failures. | 4 |
| **UX - Toast Notification Specs** | **FR-045**: System MUST define toast notification requirements: content, timing (duration), positioning, accessibility (screen reader announcements, keyboard dismissible). | 3 |
| **UX - Rapid Change Handling** | **FR-046**: System MUST define behavior for rapid successive theme changes (debouncing, queuing, or immediate updates). Strategy MUST prevent UI jank and excessive database writes. | 3 |
| **Maintainability - Code Organization** | **FR-047**: System MUST follow defined code organization structure: theme services in `app/Services/Theme/`, tests mirror source structure, consistent naming conventions. | 3 |
| **Maintainability - Documentation** | **FR-048**: System MUST maintain inline code documentation (PHPDoc blocks) and API documentation for theme-related contracts (View Composer, Livewire component, JavaScript API). | 3 |
| **Maintainability - Dependency Management** | **FR-049**: System MUST keep theme-related dependencies (Livewire, Flux, Filament) up-to-date with security patches. Dependency updates MUST be tested for compatibility. | 3 |
| **Security - Dependency Auditing** | **FR-050**: System MUST perform vulnerability scanning and dependency auditing for theme-related packages. Security vulnerabilities MUST be addressed promptly. | 3 |
| **Security - Integration Security** | **FR-051**: System MUST ensure Filament panel and Fortify authentication page theme injection does not bypass security mechanisms (CSRF, authentication, authorization). | 4 |
| **Database - Index Requirements** | **FR-052**: System MUST evaluate and document database indexing requirements for `users.settings` JSON column (if needed for query performance). | 2 |
| **Database - Schema Evolution** | **FR-053**: System MUST define backward compatibility strategy for future `users.settings` schema changes (migration path, data transformation, rollback procedures). | 3 |
| **Accessibility - Motion Preferences** | **FR-054**: System MUST respect user motion preferences (`prefers-reduced-motion`) when applying theme transitions. Animations MUST be disabled or reduced for users who prefer reduced motion. | 4 |
| **Accessibility - Color Independence** | **FR-055**: System MUST ensure theme information is not conveyed by color alone. Theme names MUST be text labels, not just color swatches. | 4 |
| **Accessibility - Error State Visibility** | **FR-056**: System MUST ensure error states, validation feedback, and success indicators remain visible and distinguishable in all theme combinations (sufficient contrast, non-color indicators). | 4 |
| **Hacker - Race Condition Handling** | **FR-057**: System MUST define explicit behavior for race conditions in theme updates (simultaneous saves from multiple tabs). Strategy MUST prevent data loss and ensure consistency. | 4 |
| **Hacker - Resource Exhaustion** | **FR-058**: System MUST define limits and handling for resource exhaustion scenarios (memory limits, CPU limits, database connection limits, JSON payload size limits). | 3 |
| **Hacker - Malformed Input** | **FR-059**: System MUST handle malformed input scenarios (malformed JSON, oversized payloads, type mismatches, SQL injection attempts in JSON). Invalid input MUST be rejected safely. | 4 |
| **Hacker - Session Storage Security** | **FR-060**: System MUST ensure preview page session storage does not leak between users or sessions. Session storage MUST be isolated per browser session. | 4 |
