# Gap Analysis: Remaining Missing Requirements from Checklists

**Created**: 2025-11-25 (Second Pass)
**Updated**: 2025-11-25 (Consolidated and Applied)
**Purpose**: Identify remaining missing requirements flagged by checklists after initial 60 requirements were added to `spec.md`
**Status**: ✅ Consolidated and applied to `spec.md`

## Consolidation Summary

The following requirements from this gap analysis were consolidated and applied to `spec.md`:

### Consolidated Requirements Applied

- **FR-024** (Enhanced): Consolidated FR-061 (Focus indicators) into existing FR-024
- **FR-054** (Enhanced): Consolidated FR-064 (Animation duration & easing) into existing FR-054
- **FR-023** (Enhanced): Consolidated FR-073 (Accessible confirmations) into existing FR-023
- **FR-045** (Enhanced): Consolidated FR-098, FR-099 (Toast accessibility & consistency) into existing FR-045
- **FR-060** (Enhanced): Consolidated FR-079 (Session storage cookie security) into existing FR-060
- **FR-061**: New - Semantic HTML requirements (from FR-062)
- **FR-062**: New - Assistive technology compatibility (from FR-063)
- **FR-063**: New - Theme label clarity & descriptions (consolidated from FR-065, FR-066)
- **FR-064**: New - Filament/Flux component accessibility (from FR-068)
- **FR-065**: New - Authentication page accessibility (from FR-069)
- **FR-066**: New - Accessibility testing requirements (from FR-070)
- **FR-067**: New - Accessibility documentation (from FR-071)
- **FR-068**: New - Accessible error messages (from FR-072)
- **FR-069**: New - Default theme accessibility (from FR-074)
- **FR-070**: New - Graceful degradation (from FR-075)
- **FR-071**: New - CSS attribute selector security (from FR-076)
- **FR-072**: New - JavaScript DOM update safety (from FR-077)
- **FR-073**: New - User settings log security & validation failure logging (consolidated from FR-078, FR-081)
- **FR-074**: New - Session fixation prevention (from FR-080)
- **FR-075**: New - Security testing requirements (from FR-082)
- **FR-076**: New - Security acceptance criteria (from FR-083)
- **FR-077**: New - Security audit logging & traceability (consolidated from FR-084, FR-085)
- **FR-078**: New - Visual design & consistency (consolidated from FR-086, FR-087, FR-088)
- **FR-079**: New - Interaction patterns & discoverability (consolidated from FR-089, FR-090, FR-091)
- **FR-080**: New - Preview page user flow & layout (consolidated from FR-092, FR-102, FR-103, FR-104)
- **FR-081**: New - Live preview visual requirements (consolidated from FR-093, FR-094, FR-095)
- **FR-082**: New - Auto-save feedback (consolidated from FR-096, FR-097)
- **FR-083**: New - Initial page load & loading states (consolidated from FR-100, FR-101)
- **FR-084**: New - Mobile responsiveness (consolidated from FR-105, FR-106, FR-107)
- **FR-085**: New - Visual hierarchy & layout (consolidated from FR-108, FR-109, FR-110)
- **FR-086**: New - State transitions & persistence (consolidated from FR-111, FR-112)
- **FR-087**: New - Theme consistency across surfaces (consolidated from FR-113, FR-114, FR-115)
- **FR-088**: New - Perceived performance & responsiveness (consolidated from FR-116, FR-117, FR-118)
- **FR-089**: New - UX edge cases (consolidated from FR-119, FR-120, FR-121)
- **FR-090**: New - UX success metrics (from FR-122)
- **FR-091**: New - JSON column structure & validation (consolidated from FR-123, FR-124, FR-127)
- **FR-092**: New - Enum serialization & deserialization (consolidated from FR-125, FR-126)
- **FR-093**: New - Theme/Flavor relationship integrity (consolidated from FR-128, FR-129, FR-140)
- **FR-094**: New - Default value handling (consolidated from FR-130, FR-131, FR-132)
- **FR-095**: New - Auto-save trigger & optimization (consolidated from FR-135, FR-136, FR-134)
- **FR-096**: New - Data consistency & state synchronization (consolidated from FR-137, FR-138, FR-139)
- **FR-097**: New - Validation error handling & consistency (consolidated from FR-143, FR-144)
- **FR-098**: New - Database migration requirements (consolidated from FR-141, FR-142)
- **FR-099**: New - Telescope configuration (consolidated from FR-152, FR-153, FR-154, FR-155, FR-156)
- **FR-100**: New - Horizon configuration (consolidated from FR-157, FR-158, FR-159, FR-160)
- **FR-101**: New - Performance metric collection & instrumentation (consolidated from FR-161, FR-162, FR-163, FR-164)
- **FR-102**: New - Validation correction tracking (consolidated from FR-165, FR-166, FR-167)
- **FR-103**: New - Preview interaction tracking (consolidated from FR-168, FR-169, FR-170)
- **FR-104**: New - Error tracking & alerting (consolidated from FR-172, FR-173, FR-174)
- **FR-105**: New - Observability dashboards (consolidated from FR-178, FR-179, FR-180)
- **FR-106**: New - Alerting configuration (consolidated from FR-181, FR-182, FR-183, FR-184)
- **FR-107**: New - Observability testing (consolidated from FR-185, FR-186, FR-187)
- **FR-108**: New - Observability setup & configuration (consolidated from FR-188, FR-189, FR-190, FR-191)
- **FR-109**: New - Performance target scope & consistency (consolidated from FR-192, FR-193)
- **FR-110**: New - Initial page load performance (consolidated from FR-194, FR-195, FR-196, FR-197)
- **FR-111**: New - Database performance (consolidated from FR-198, FR-199, FR-200, FR-201)
- **FR-112**: New - Client-side performance (consolidated from FR-202, FR-203, FR-204)
- **FR-113**: New - Device & browser performance (consolidated from FR-205, FR-206)
- **FR-114**: New - Scalability & resource usage (consolidated from FR-207, FR-208, FR-209, FR-210)
- **FR-115**: New - Performance degradation handling (consolidated from FR-211, FR-212)
- **FR-116**: New - Performance testing methodology (consolidated from FR-213, FR-214, FR-215)
- **FR-117**: New - Performance monitoring (consolidated from FR-216, FR-217, FR-218, FR-219)
- **FR-118**: New - Performance optimization (consolidated from FR-220, FR-221, FR-222, FR-223, FR-224)
- **FR-119**: New - Preview page performance (consolidated from FR-225, FR-226, FR-227, FR-228)
- **FR-120**: New - Performance acceptance criteria (consolidated from FR-229, FR-230, FR-231)
- **FR-030** (Enhanced): Consolidated FR-147, FR-148 (Account deletion, data lifecycle) into existing FR-030
- **FR-036** (Enhanced): Consolidated FR-149, FR-150, FR-151 (Event tracking details) into existing FR-036
- **FR-037** (Enhanced): Consolidated FR-171 (Sensitive data logging) into existing FR-037
- **FR-038** (Enhanced): Consolidated FR-175, FR-176, FR-177 (Log format, aggregation, rotation) into existing FR-038

### Total Consolidated

- **Original gap items**: 171
- **Consolidated into**: 60 new/updated requirements
- **Consolidation ratio**: ~2.85:1 (171 items → 60 requirements)

## Original Gap Analysis (Pre-Consolidation)

> **Note**: All 171 requirements below were consolidated into 60 new/updated requirements and applied to `spec.md`. See "Consolidation Summary" section above for complete mapping.
>
> **Table Format**: The table below shows original gap items. Rows with 5 columns show the consolidation mapping. Rows with 3 columns reference the consolidation summary above for mapping details (all items were successfully consolidated and applied).

| Missing Checklist Item | Original Proposed Requirement | Consolidated Into | Confidence Score (1-5) | Status |
| :--- | :--- | :--- | :--- | :--- |
| **Focus Indicator Requirements** | System MUST provide visible focus indicators (focus rings) with sufficient contrast for all interactive theme selection controls. Focus indicators MUST be visible in all theme combinations. | **FR-024** (Enhanced) | 5 | ✅ Applied |
| **Semantic HTML Requirements** | System MUST use semantic HTML for theme selection UI (proper form elements, fieldset/legend grouping for related controls, appropriate input types). | **FR-061** | 4 | ✅ Applied |
| **Assistive Technology Compatibility** | System MUST ensure theme data attributes (`data-theme`, `data-flavor`, `data-accent`) do not interfere with assistive technology parsing. Data attributes MUST be used only for styling, not for semantic meaning. | **FR-062** | 4 | ✅ Applied |
| **Animation Duration & Easing** | System MUST define animation duration and easing requirements for theme transitions to avoid triggering vestibular disorders. Animations MUST not exceed 500ms duration and MUST use ease-in-out or similar smooth easing. | **FR-054** (Enhanced) | 4 | ✅ Applied |
| **Theme Label Clarity** | System MUST use clear, non-technical language for theme labels (e.g., "Dark Mode" vs "Mocha Flavor") to support users with cognitive disabilities. Technical terms MUST be accompanied by plain-language descriptions. | **FR-063** | 4 | ✅ Applied |
| **Theme Descriptions/Previews** | System MUST provide theme descriptions or visual previews (beyond color names) to help users make informed theme choices. Each theme/flavor combination MUST have a brief description or visual example. | **FR-063** | 3 | ✅ Applied |
| **Theme Selection UI Organization** | System MUST ensure theme selection UI is not overwhelming (grouping related controls, progressive disclosure if needed, clear visual hierarchy). UI MUST follow established design patterns for settings pages. | **FR-079** | 3 | ✅ Applied |
| **Filament/Flux Component Accessibility** | System MUST ensure Filament and Flux components maintain accessibility when themed (component focus states, ARIA attributes, keyboard navigation). Theme changes MUST not break component accessibility features. | **FR-064** | 4 | ✅ Applied |
| **Authentication Page Accessibility** | System MUST ensure authentication pages (Fortify) remain accessible when themed (contrast requirements met, focus indicators visible, form labels readable). Theme application MUST not degrade accessibility of authentication forms. | **FR-065** | 4 | ✅ Applied |
| **Accessibility Testing Requirements** | System MUST include accessibility testing requirements (automated tools like axe-core, manual testing, screen reader testing with NVDA/JAWS/VoiceOver). All theme combinations MUST pass accessibility validation. | **FR-066** | 4 | ✅ Applied |
| **Accessibility Documentation** | System MUST document accessibility features and limitations of each theme combination. Documentation MUST include contrast ratios, keyboard navigation support, and screen reader compatibility for each combination. | **FR-067** | 3 | ✅ Applied |
| **Accessible Error Messages** | System MUST provide accessible error messages when theme validation fails (screen reader announcements via live regions, visible text, sufficient contrast). Error messages MUST be announced to assistive technology users. | **FR-068** | 4 | ✅ Applied |
| **Accessible Theme Change Confirmations** | System MUST ensure theme change confirmations or feedback are accessible (not just visual toasts). Success messages MUST be announced to screen readers via live regions. | **FR-023** (Enhanced) | 4 | ✅ Applied |
| **Default Theme Accessibility** | System MUST ensure the default theme (Catppuccin Mocha) meets accessibility standards out of the box (WCAG AA contrast, keyboard navigation, screen reader support). Default theme MUST be fully accessible without user configuration. | **FR-069** | 4 | ✅ Applied |
| **Graceful Degradation** | System MUST ensure graceful degradation when CSS or JavaScript fails (theme still readable, no broken layouts, content remains accessible). Application MUST function with themes even if JavaScript is disabled. | **FR-070** | 4 | ✅ Applied |
| **CSS Attribute Selector Security** | System MUST validate that CSS attribute selectors cannot be exploited (preventing injection of malicious attribute values that could break CSS parsing or cause XSS). Attribute values MUST be validated against allowed enum values before rendering. | **FR-071** | 4 | ✅ Applied |
| **JavaScript DOM Update Safety** | System MUST ensure JavaScript updates to DOM attributes are safe (no eval, no innerHTML manipulation, use safe DOM methods like `setAttribute` or `dataset`). All client-side theme updates MUST use safe DOM manipulation methods. | **FR-072** | 4 | ✅ Applied |
| **User Settings Log Security** | System MUST ensure user settings data (theme preferences) are not exposed in application logs or error messages. Logging of user preferences MUST be disabled or anonymized. | **FR-073** | 4 | ✅ Applied |
| **Session Storage Cookie Security** | System MUST ensure session storage on preview page uses secure, HttpOnly cookies if cookies are involved (or use sessionStorage API with proper isolation). Session data MUST not be accessible via JavaScript from other origins. | **FR-060** (Enhanced) | 4 | ✅ Applied |
| **Session Fixation Prevention** | System MUST prevent session fixation attacks when users transition from preview page to authenticated state. Session regeneration MUST occur on authentication to prevent session hijacking. | **FR-074** | 4 | ✅ Applied |
| **Validation Failure Logging** | System MUST ensure validation failures are logged securely without exposing user data (log event type and correction action, not actual theme values or user identifiers). Validation logs MUST be anonymized. | **FR-073** | 3 | ✅ Applied |
| **Security Testing Requirements** | System MUST include security testing requirements (penetration testing for XSS/CSRF, vulnerability scanning, security code review). Security tests MUST verify all security requirements are met. | **FR-075** | 4 | ✅ Applied |
| **Security Acceptance Criteria** | System MUST define security acceptance criteria (e.g., "All inputs validated, all outputs encoded, no XSS vulnerabilities, CSRF protection verified"). Security requirements MUST have measurable acceptance criteria. | **FR-076** | 4 | ✅ Applied |
| **Security Audit Logging** | System MUST implement audit logging of security-relevant events (failed validations, unauthorized access attempts, rate limit violations). Security audit logs MUST be separate from application logs and retained per compliance requirements. | **FR-077** | 4 | ✅ Applied |
| **Theme Preference Change Traceability** | System MUST ensure theme preference changes are traceable for security incident investigation (log user id, timestamp, previous value, new value, source IP). Change logs MUST be retained for security audit purposes. | **FR-077** | 4 | ✅ Applied |
| **Visual Design Requirements** | System MUST define visual design requirements for theme application (color schemes, typography, spacing, visual hierarchy). Themes MUST maintain consistent visual design principles across all combinations. | See Consolidation Summary | 3 | ✅ Applied |
| **Visual Consistency Across Surfaces** | System MUST ensure visual consistency across all application surfaces (Folio pages, Filament panels, Fortify auth pages). Theme appearance MUST be visually consistent regardless of which surface is rendered. | See Consolidation Summary | 3 | ✅ Applied |
| **Visual Hierarchy Maintenance** | System MUST maintain visual hierarchy when themes change (headings, body text, interactive elements remain distinguishable). Theme changes MUST not degrade visual hierarchy or make content harder to scan. | See Consolidation Summary | 3 | ✅ Applied |
| **Interaction Pattern Definition** | System MUST define interaction requirements for appearance settings UI (how users select theme/flavor/accent - dropdowns, radio buttons, cards, or other controls). Interaction patterns MUST be consistent and intuitive. | See Consolidation Summary | 3 | ✅ Applied |
| **Theme Selection Discoverability** | System MUST ensure theme selection controls are intuitive and discoverable (clear labels, visual previews, logical grouping). Users MUST be able to find and use theme controls without training. | See Consolidation Summary | 3 | ✅ Applied |
| **Settings Page Navigation** | System MUST define user flow requirements for navigating to the appearance settings page (where it is located in navigation, how users discover it). Settings page MUST be easily discoverable from main navigation or user menu. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Page User Flow** | System MUST define preview page user flow (how visitors discover it, what they can do, how it differs from authenticated settings). Preview page MUST be discoverable and clearly communicate its purpose. | See Consolidation Summary | 3 | ✅ Applied |
| **Live Preview Visual Definition** | System MUST define what "immediate live preview" means visually (instant color changes, smooth transitions, no flicker). Live preview MUST provide immediate visual feedback without visual artifacts. | See Consolidation Summary | 3 | ✅ Applied |
| **Live Preview Smoothness** | System MUST ensure live preview updates are smooth and performant (no jank, no layout shifts during theme changes). Theme transitions MUST use hardware-accelerated CSS transitions when possible. | See Consolidation Summary | 3 | ✅ Applied |
| **Visual Feedback During Changes** | System MUST provide visual feedback when theme changes occur (e.g., subtle animation, color transition, visual confirmation). Users MUST receive clear visual indication that theme change was successful. | See Consolidation Summary | 3 | ✅ Applied |
| **Auto-Save Success Feedback** | System MUST define user feedback when auto-save succeeds (toast notification, visual indicator, or silent). Success feedback MUST be provided unless explicitly designed to be silent. | See Consolidation Summary | 3 | ✅ Applied |
| **Auto-Save User Understanding** | System MUST ensure users understand their preferences are saved automatically (no confusion about needing to click "Save"). UI MUST clearly communicate that changes are saved automatically. | See Consolidation Summary | 3 | ✅ Applied |
| **Toast Notification Accessibility** | System MUST ensure toast notifications are accessible (screen reader announcements, keyboard dismissible, sufficient contrast). Toast notifications MUST not rely solely on visual indicators. | See Consolidation Summary | 4 | ✅ Applied |
| **Toast Notification Consistency** | System MUST ensure toast notification behavior is consistent across different pages (consistent styling, positioning, duration). Toast notifications MUST provide consistent user experience across all application surfaces. | See Consolidation Summary | 3 | ✅ Applied |
| **Initial Page Load UX** | System MUST define initial page load experience requirements (server-side injection prevents FOUC, but loading states may be needed for slow connections). Initial load MUST provide appropriate feedback to users. | See Consolidation Summary | 3 | ✅ Applied |
| **Loading States for Preferences** | System MUST define loading states when theme preferences are being fetched (skeleton, spinner, or immediate render). Loading states MUST prevent layout shift and provide user feedback. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Page Layout** | System MUST define preview page layout and visual design requirements (how theme controls are presented, what content is shown). Preview page MUST have clear, intuitive layout that matches authenticated settings UI where appropriate. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Mode Indication** | System MUST provide visual indication that preview page changes are temporary (e.g., "Preview Mode" banner, different styling, clear messaging). Users MUST understand that preview changes will not persist. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Navigation Warning** | System MUST define user feedback when navigating away from preview page (should users be warned changes won't persist, or is silent reset acceptable). Navigation away from preview MUST be handled gracefully. | See Consolidation Summary | 3 | ✅ Applied |
| **Mobile Responsiveness** | System MUST ensure theme selection UI works on mobile devices (touch targets minimum 44x44px, layout adapts to small screens, spacing appropriate for touch). Mobile experience MUST be fully functional and accessible. | See Consolidation Summary | 4 | ✅ Applied |
| **Settings Page Responsiveness** | System MUST define responsive behavior requirements for appearance settings page (mobile vs. desktop layout, breakpoints, adaptive UI). Settings page MUST be fully usable on all device sizes. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Page Responsiveness** | System MUST ensure preview page is responsive and usable on all device sizes (mobile, tablet, desktop). Preview page MUST provide consistent experience across device types. | See Consolidation Summary | 3 | ✅ Applied |
| **Visual Hierarchy of Controls** | System MUST define visual hierarchy of theme selection controls (which is most prominent - Theme, Flavor, or Accent). Visual hierarchy MUST guide users through selection process logically. | See Consolidation Summary | 3 | ✅ Applied |
| **Control Layout & Spacing** | System MUST define layout and spacing requirements for theme selection controls (grouping, alignment, visual relationships). Controls MUST be visually organized and easy to scan. | See Consolidation Summary | 3 | ✅ Applied |
| **Theme Preview Swatches** | System MUST provide theme previews or swatches (color samples, visual examples of each theme). Users MUST be able to preview themes before selecting them. | See Consolidation Summary | 3 | ✅ Applied |
| **State Transitions** | System MUST define state transition requirements when theme changes (smooth color transitions, fade effects, or instant swap). Transitions MUST be visually pleasing and not jarring. | See Consolidation Summary | 3 | ✅ Applied |
| **Theme State Persistence** | System MUST ensure theme state persists correctly across page navigation (no flicker, no reset to default). Theme preferences MUST remain consistent during user session. | See Consolidation Summary | 4 | ✅ Applied |
| **Filament Theme Consistency** | System MUST ensure theme appearance is consistent between Filament admin panels and main application pages. Filament panels MUST use same theme data attributes and visual styling as main application. | See Consolidation Summary | 3 | ✅ Applied |
| **Fortify Theme Consistency** | System MUST ensure theme appearance is consistent between Fortify authentication pages and main application. Authentication pages MUST use same theme data attributes and visual styling as main application. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Page Behavior Consistency** | System MUST ensure preview page theme switching matches the visual behavior of authenticated settings (same color changes, same transitions). Preview page MUST provide accurate representation of theme changes. | See Consolidation Summary | 3 | ✅ Applied |
| **Perceived Performance** | System MUST define perceived performance requirements for theme changes (should transitions be instant or animated, what feels "fast"). Perceived performance MUST meet user expectations for responsiveness. | See Consolidation Summary | 3 | ✅ Applied |
| **High Latency Responsiveness** | System MUST ensure theme changes feel responsive even if network latency is high (optimistic updates, client-side first). Theme changes MUST provide immediate visual feedback regardless of network conditions. | See Consolidation Summary | 3 | ✅ Applied |
| **Visual Feedback During Operations** | System MUST provide visual feedback during theme change operations (loading indicators, progress, or instant visual update). Users MUST receive feedback that theme change is in progress or complete. | See Consolidation Summary | 3 | ✅ Applied |
| **No Preferences UX** | System MUST define UX when user has no saved preferences (default theme applied, clear indication of default state). Users MUST understand they are using default theme and can customize it. | See Consolidation Summary | 3 | ✅ Applied |
| **Corrupted Data UX** | System MUST define UX when theme data is corrupted or invalid (silent correction, but should UI reflect the correction visually). Users MUST not experience visual glitches when corrupted data is corrected. | See Consolidation Summary | 3 | ✅ Applied |
| **Concurrent Changes UX** | System MUST define UX for handling concurrent theme changes (user changes theme while another tab is open - should changes sync). Multiple tabs MUST maintain theme consistency or clearly indicate when they differ. | See Consolidation Summary | 3 | ✅ Applied |
| **UX Success Metrics** | System MUST define UX success metrics beyond latency (user satisfaction, error rates, task completion time). UX requirements MUST have measurable success criteria. | See Consolidation Summary | 3 | ✅ Applied |
| **JSON Column Structure** | System MUST define explicit requirements for JSON column structure in `users.settings` (required fields: theme, flavor, accent; optional fields: none for theme preferences; nested structure definition). JSON structure MUST be formally documented. | See Consolidation Summary | 4 | ✅ Applied |
| **JSON Column Nullability** | System MUST ensure the `users.settings` JSON column remains nullable (allowing null for new users). Null values MUST be handled consistently across all code paths. | See Consolidation Summary | 4 | ✅ Applied |
| **Enum Serialization Validation** | System MUST ensure enum serialization produces valid JSON values (string values match enum cases exactly). Serialization MUST be verified to prevent data corruption. | See Consolidation Summary | 4 | ✅ Applied |
| **Enum Deserialization Handling** | System MUST define requirements for handling enum deserialization failures (invalid enum values in JSON, corrupted data). Deserialization failures MUST trigger validation and correction logic. | See Consolidation Summary | 4 | ✅ Applied |
| **JSON Structure Validation** | System MUST define JSON structure validation requirements (ensuring required fields exist, no extra fields cause issues). JSON structure MUST be validated before use. | See Consolidation Summary | 4 | ✅ Applied |
| **Theme/Flavor Relationship Integrity** | System MUST enforce relationship integrity between Theme and ThemeFlavor (flavors MUST belong to their theme, invalid combinations MUST be rejected). Relationship integrity MUST be validated at application level. | See Consolidation Summary | 4 | ✅ Applied |
| **Relationship Change Handling** | System MUST define requirements for handling theme/flavor relationship changes (what happens if enum relationships change after data is persisted). Relationship changes MUST have migration strategy. | See Consolidation Summary | 3 | ✅ Applied |
| **Default Value Initialization** | System MUST define requirements for default value initialization when `users.settings` is null (when exactly does this occur - on first access, on boot, on save). Default initialization MUST be consistent and documented. | See Consolidation Summary | 4 | ✅ Applied |
| **Default Value Consistency** | System MUST ensure default values are consistent across all code paths (booted(), View Composer, Livewire component). All code paths MUST use same default values. | See Consolidation Summary | 4 | ✅ Applied |
| **Partial Null Handling** | System MUST define requirements for handling partial null values in JSON (e.g., theme set but flavor null - should defaults apply to missing fields). Partial nulls MUST be handled consistently. | See Consolidation Summary | 4 | ✅ Applied |
| **Database Write Atomicity** | System MUST ensure database writes are atomic (all-or-nothing, no partial updates). Auto-save operations MUST use transactions to ensure atomicity. | See Consolidation Summary | 4 | ✅ Applied |
| **Database Save Failure Handling** | System MUST define requirements for handling database save failures (retry logic, error handling, user notification). Save failures MUST be handled gracefully with appropriate user feedback. | See Consolidation Summary | 4 | ✅ Applied |
| **Auto-Save Trigger Definition** | System MUST define exact requirements for when auto-save triggers (immediately on property change, debounced after Xms, batched). Auto-save trigger behavior MUST be explicitly defined and consistent. | See Consolidation Summary | 4 | ✅ Applied |
| **Auto-Save Write Optimization** | System MUST ensure auto-save does not cause excessive database writes (rate limiting, debouncing, or batching). Auto-save MUST be optimized to prevent database overload. | See Consolidation Summary | 4 | ✅ Applied |
| **Data Consistency Requirements** | System MUST ensure data consistency between database and in-memory state (User model, Livewire component, View Composer). All components MUST see consistent theme state. | See Consolidation Summary | 4 | ✅ Applied |
| **State Synchronization** | System MUST define requirements for handling state synchronization when theme changes occur (ensuring all components see updated state). State synchronization MUST be reliable and immediate. | See Consolidation Summary | 4 | ✅ Applied |
| **Multi-Path Update Consistency** | System MUST ensure data consistency when user settings are updated via multiple paths (Livewire component, direct model update, migration). All update paths MUST produce consistent results. | See Consolidation Summary | 4 | ✅ Applied |
| **ThemeAccent Independence** | System MUST ensure ThemeAccent remains independent of Theme/Flavor (no referential constraints needed). Accent selection MUST be independent of theme/flavor choice. | See Consolidation Summary | 4 | ✅ Applied |
| **No Migration Requirement** | System MUST explicitly state that no database migrations are required for this feature (uses existing `users.settings` column). Feature MUST not require schema changes. | See Consolidation Summary | 4 | ✅ Applied |
| **Data Migration Scenarios** | System MUST define data migration scenarios (if enum values change, how are existing records handled). Enum value changes MUST have migration strategy. | See Consolidation Summary | 3 | ✅ Applied |
| **Validation Error Handling** | System MUST define validation error handling requirements (what happens if validation fails during save - prevent save, log error, notify user). Validation failures MUST be handled consistently. | See Consolidation Summary | 4 | ✅ Applied |
| **Validation Rule Consistency** | System MUST ensure validation rules are consistent across all entry points (Livewire component, View Composer, direct model updates). All entry points MUST use same validation logic. | See Consolidation Summary | 4 | ✅ Applied |
| **Database Integrity Testing** | System MUST include database integrity testing requirements (test invalid combinations, corrupted data, concurrent updates). Database integrity MUST be verified through comprehensive testing. | See Consolidation Summary | 4 | ✅ Applied |
| **Database Integrity Acceptance Criteria** | System MUST define acceptance criteria for database integrity (e.g., "No invalid theme/flavor combinations persist", "All corrupted data auto-corrected"). Database integrity MUST have measurable success criteria. | See Consolidation Summary | 4 | ✅ Applied |
| **Account Deletion Handling** | System MUST define requirements for handling user account deletion (should theme preferences be deleted, archived, or retained). Account deletion MUST have defined data retention policy. | See Consolidation Summary | 3 | ✅ Applied |
| **Data Lifecycle Management** | System MUST define data lifecycle management requirements (cleanup of orphaned or invalid data). Data lifecycle MUST be managed proactively. | See Consolidation Summary | 3 | ✅ Applied |
| **Event Timestamp Handling** | System MUST define event timestamp and timezone handling requirements (when events are recorded, timezone consistency). Event timestamps MUST be consistent and timezone-aware. | See Consolidation Summary | 3 | ✅ Applied |
| **Event Correlation** | System MUST define event correlation requirements (how to link related events, trace IDs, request IDs). Related events MUST be correlatable for debugging and analysis. | See Consolidation Summary | 3 | ✅ Applied |
| **Event Sampling** | System MUST define event sampling or rate limiting requirements (should all events be tracked or sampled). Event tracking MUST not impact application performance. | See Consolidation Summary | 3 | ✅ Applied |
| **Telescope Capture Requirements** | System MUST define specific requirements for what Telescope should capture (request/log tracing, specific events, performance markers). Telescope configuration MUST be explicitly defined. | See Consolidation Summary | 3 | ✅ Applied |
| **Telescope Dashboard Configuration** | System MUST define Telescope dashboard configuration requirements (what dashboards are needed, what metrics displayed). Telescope dashboards MUST be configured for theme event monitoring. | See Consolidation Summary | 3 | ✅ Applied |
| **Telescope Data Retention** | System MUST define Telescope data retention policies (how long to keep logs, storage limits). Telescope data retention MUST be configured to balance storage costs and debugging needs. | See Consolidation Summary | 3 | ✅ Applied |
| **Telescope Filtering** | System MUST define Telescope filtering and search capabilities (how to query theme events, filter by user/session). Telescope MUST support filtering theme events for debugging. | See Consolidation Summary | 3 | ✅ Applied |
| **Telescope Performance Impact** | System MUST define Telescope performance impact requirements (should observability affect application performance, acceptable overhead). Telescope overhead MUST be measured and acceptable. | See Consolidation Summary | 3 | ✅ Applied |
| **Horizon Configuration** | System MUST define Horizon configuration requirements (when should it be configured, what metrics surfaced). Horizon MUST be configured if queues are used for theme operations. | See Consolidation Summary | 3 | ✅ Applied |
| **Horizon Queue Metrics** | System MUST define Horizon queue metrics requirements (what queue metrics are relevant for theming, when are queues used). Queue metrics MUST be defined if background jobs are introduced. | See Consolidation Summary | 3 | ✅ Applied |
| **Horizon Dashboard Setup** | System MUST define Horizon dashboard setup requirements (what dashboards, what metrics displayed). Horizon dashboards MUST be configured for queue monitoring if queues are used. | See Consolidation Summary | 3 | ✅ Applied |
| **Horizon Optional Configuration** | System MUST define requirements for handling Horizon when no queues are used (is Horizon optional, should it be disabled). Horizon configuration MUST be appropriate for actual queue usage. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Metric Collection** | System MUST define performance metric collection requirements (p95 latency, what other metrics, measurement points). Performance metrics MUST be collected systematically. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Instrumentation** | System MUST define performance instrumentation implementation requirements (custom events, timing calls, where to instrument). Performance instrumentation MUST be implemented consistently. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Data Queryability** | System MUST define requirements for making performance data queryable (Telescope dashboards, API endpoints, export capabilities). Performance data MUST be accessible for analysis. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Regression Detection** | System MUST define performance regression detection requirements (alerts, thresholds, notification mechanisms). Performance regressions MUST be detected and alerted automatically. | See Consolidation Summary | 3 | ✅ Applied |
| **Invalid Combination Recording** | System MUST define requirements for recording invalid theme combinations (what was invalid, what was corrected to). Invalid combinations MUST be logged for analysis and debugging. | See Consolidation Summary | 3 | ✅ Applied |
| **Correction Frequency Tracking** | System MUST define requirements for tracking correction frequency (how often corrections occur, per user, globally). Correction frequency MUST be tracked to identify data quality issues. | See Consolidation Summary | 3 | ✅ Applied |
| **Correction Rate Alerting** | System MUST define requirements for alerting on high correction rates (thresholds, notification mechanisms). High correction rates MUST trigger alerts for investigation. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Usage Pattern Tracking** | System MUST define requirements for tracking preview page usage patterns (how many visitors, theme preferences, session duration). Preview usage MUST be tracked for analytics. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Conversion Correlation** | System MUST define requirements for correlating preview interactions with conversions (do preview visitors sign up, link preview to authenticated usage). Preview-to-conversion correlation MUST be tracked. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Performance Tracking** | System MUST define requirements for tracking preview page performance (load times, interaction latency, error rates). Preview performance MUST be monitored. | See Consolidation Summary | 3 | ✅ Applied |
| **Sensitive Data Logging Prevention** | System MUST ensure sensitive data is not logged (passwords, tokens, personal information). Logging MUST exclude sensitive user data. | See Consolidation Summary | 4 | ✅ Applied |
| **Error Context Logging** | System MUST define requirements for error context in logs (stack traces, request context, user context). Error logs MUST include sufficient context for debugging without exposing sensitive data. | See Consolidation Summary | 3 | ✅ Applied |
| **Error Alerting** | System MUST define error alerting requirements (when to alert, severity levels, notification channels). Error alerts MUST be configured appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Error Rate Tracking** | System MUST define requirements for tracking error rates and trends (error frequency, error types, resolution tracking). Error rates MUST be monitored for trends. | See Consolidation Summary | 3 | ✅ Applied |
| **Log Format & Structure** | System MUST define log format and structure requirements (JSON, structured logging, field names, consistency). Logs MUST use consistent structured format. | See Consolidation Summary | 3 | ✅ Applied |
| **Log Aggregation** | System MUST define log aggregation and storage requirements (where logs are stored, aggregation service, search capabilities). Log aggregation MUST be configured for centralized logging. | See Consolidation Summary | 3 | ✅ Applied |
| **Log Rotation** | System MUST define log rotation and archival requirements (retention, archival policies, access to historical logs). Log rotation MUST be configured to manage storage. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Dashboards** | System MUST define observability dashboard requirements (what dashboards are needed, what metrics displayed). Observability dashboards MUST be configured for theme monitoring. | See Consolidation Summary | 3 | ✅ Applied |
| **Real-Time vs Historical Metrics** | System MUST define requirements for real-time vs. historical metrics (live dashboards, historical analysis, time ranges). Metrics MUST be available in both real-time and historical views. | See Consolidation Summary | 3 | ✅ Applied |
| **Dashboard Access Control** | System MUST define dashboard access control requirements (who can view dashboards, authentication, authorization). Dashboard access MUST be restricted appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Alert Conditions** | System MUST define alert conditions (when to alert, thresholds, conditions). Alert conditions MUST be explicitly defined. | See Consolidation Summary | 3 | ✅ Applied |
| **Alert Channels** | System MUST define alert channel requirements (email, Slack, PagerDuty, notification mechanisms). Alert channels MUST be configured appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Alert Severity Levels** | System MUST define alert severity level requirements (critical, warning, info, how to classify). Alert severity MUST be classified consistently. | See Consolidation Summary | 3 | ✅ Applied |
| **Alert Deduplication** | System MUST define alert deduplication and rate limiting requirements (prevent alert storms, grouping similar alerts). Alert storms MUST be prevented. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Testing** | System MUST define requirements for testing observability implementation (how to verify events are captured, metrics recorded). Observability MUST be testable. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Acceptance Criteria** | System MUST define observability acceptance criteria (what constitutes successful observability implementation). Observability MUST have measurable success criteria. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Regression Testing** | System MUST define observability regression testing requirements (ensuring observability doesn't break when code changes). Observability MUST be maintained through code changes. | See Consolidation Summary | 3 | ✅ Applied |
| **Telescope/Horizon Setup** | System MUST define Telescope and Horizon setup and configuration requirements (installation, configuration steps, environment setup). Telescope and Horizon MUST be properly configured. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Environment Configuration** | System MUST define observability requirements for different environments (development, staging, production - same or different configs). Observability MUST be configured appropriately per environment. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Feature Flags** | System MUST define observability feature flag requirements (can observability be disabled, environment-specific toggles). Observability MUST be controllable via feature flags if needed. | See Consolidation Summary | 3 | ✅ Applied |
| **Observability Performance Overhead** | System MUST define observability performance overhead requirements (acceptable impact on application performance, resource usage). Observability overhead MUST be acceptable. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Target Scope** | System MUST define performance target scope (client-side DOM updates only, or including server-side processing). Performance targets MUST be clearly scoped. | See Consolidation Summary | 3 | ✅ Applied |
| **Theme Change Performance Consistency** | System MUST define requirements for theme change performance consistency (should performance be consistent across all theme combinations). Performance MUST be consistent regardless of theme combination. | See Consolidation Summary | 3 | ✅ Applied |
| **Initial Page Load Performance** | System MUST define initial page load performance requirements (time to first paint, time to interactive, FOUC prevention). Initial load performance MUST meet defined targets. | See Consolidation Summary | 3 | ✅ Applied |
| **Server-Side Injection Performance** | System MUST define server-side theme injection performance requirements (View Composer overhead, database query time). Server-side injection MUST not significantly impact page load time. | See Consolidation Summary | 3 | ✅ Applied |
| **No Settings Load Performance** | System MUST define initial load performance requirements when user has no saved settings (default theme application speed). Default theme application MUST be fast. | See Consolidation Summary | 3 | ✅ Applied |
| **Invalid Settings Load Performance** | System MUST define initial load performance requirements when user has invalid settings (validation and correction overhead). Validation and correction MUST not significantly impact load time. | See Consolidation Summary | 3 | ✅ Applied |
| **Database Write Performance** | System MUST define database write performance requirements during auto-save (latency, throughput, acceptable overhead). Auto-save MUST not significantly impact user experience. | See Consolidation Summary | 3 | ✅ Applied |
| **Database Query Performance** | System MUST define database query performance requirements when reading user settings (query time, caching requirements). Settings queries MUST be fast and cached appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Concurrent Update Performance** | System MUST define database performance requirements under concurrent theme updates (multiple tabs, simultaneous saves). Concurrent updates MUST not cause performance degradation. | See Consolidation Summary | 3 | ✅ Applied |
| **Validation Performance** | System MUST define database performance requirements when validation occurs (validation overhead, correction persistence time). Validation MUST be fast and not impact user experience. | See Consolidation Summary | 3 | ✅ Applied |
| **JavaScript Execution Performance** | System MUST define JavaScript execution performance requirements (DOM update time, attribute setting overhead). Client-side theme updates MUST be fast. | See Consolidation Summary | 3 | ✅ Applied |
| **CSS Application Performance** | System MUST define CSS application performance requirements (attribute selector matching time, style recalculation overhead). CSS application MUST be fast and efficient. | See Consolidation Summary | 3 | ✅ Applied |
| **Browser Rendering Performance** | System MUST define browser rendering performance requirements (repaint time, reflow prevention, layout shift avoidance). Theme changes MUST not cause layout shifts or jank. | See Consolidation Summary | 3 | ✅ Applied |
| **Device Performance Targets** | System MUST define client-side performance targets for different devices (mobile, tablet, desktop performance targets). Performance MUST be acceptable on all device types. | See Consolidation Summary | 3 | ✅ Applied |
| **Browser Performance Compatibility** | System MUST define client-side performance requirements for different browsers (Chrome, Firefox, Safari compatibility and performance). Performance MUST be acceptable across major browsers. | See Consolidation Summary | 3 | ✅ Applied |
| **Network Bandwidth Usage** | System MUST define network bandwidth usage requirements (CSS file sizes, JavaScript bundle sizes, asset loading). Asset sizes MUST be optimized. | See Consolidation Summary | 3 | ✅ Applied |
| **Scalability Requirements** | System MUST define scalability requirements (performance under high user load, concurrent theme changes). System MUST scale to handle expected user load. | See Consolidation Summary | 3 | ✅ Applied |
| **New Theme Performance Impact** | System MUST define resource usage requirements when adding new themes (should performance degrade, acceptable overhead). Adding themes MUST not significantly impact performance. | See Consolidation Summary | 3 | ✅ Applied |
| **Concurrent User Load Performance** | System MUST define performance requirements under concurrent user load (multiple users changing themes simultaneously). System MUST handle concurrent usage without degradation. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Degradation Scenarios** | System MUST define performance degradation scenario requirements (what happens when system is under stress). Performance degradation MUST be handled gracefully. | See Consolidation Summary | 3 | ✅ Applied |
| **Graceful Performance Degradation** | System MUST define graceful performance degradation requirements (should features degrade gracefully or fail fast). Performance issues MUST be handled appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Testing Methodology** | System MUST define performance testing methodology requirements (load testing, stress testing, benchmark testing). Performance testing MUST use appropriate methodologies. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Test Scenarios** | System MUST define performance test scenario requirements (what scenarios must be tested - normal load, high load, edge cases). Performance tests MUST cover relevant scenarios. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Test Environments** | System MUST define performance test environment requirements (should tests run in production-like environments). Performance tests MUST run in appropriate environments. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Monitoring Implementation** | System MUST define performance monitoring implementation requirements (what tools, what metrics, how often). Performance MUST be monitored systematically. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Data Storage** | System MUST define performance data collection and storage requirements (where performance data is stored, retention policies). Performance data MUST be stored appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Dashboard Requirements** | System MUST define performance dashboard requirements (what dashboards, what metrics displayed, real-time vs. historical). Performance dashboards MUST be configured. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Alerting** | System MUST define performance alerting requirements (when to alert on performance degradation, thresholds, notification channels). Performance alerts MUST be configured. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Optimization Guidelines** | System MUST define performance optimization guideline requirements (when to optimize, acceptable trade-offs). Performance optimization MUST follow defined guidelines. | See Consolidation Summary | 3 | ✅ Applied |
| **Caching Requirements** | System MUST define caching requirements (should theme data be cached, cache invalidation strategy). Theme data MUST be cached appropriately. | See Consolidation Summary | 3 | ✅ Applied |
| **Lazy Loading Requirements** | System MUST define lazy loading requirements (should theme assets be lazy loaded, deferred loading). Asset loading MUST be optimized. | See Consolidation Summary | 3 | ✅ Applied |
| **Code Splitting Requirements** | System MUST define code splitting requirements (should theme code be split into separate bundles). Code splitting MUST be considered for optimization. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Optimization Priorities** | System MUST define performance optimization priority requirements (what optimizations are most important). Optimization priorities MUST be defined. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Page Load Performance** | System MUST define preview page load performance requirements (initial load time, theme switching latency). Preview page MUST load quickly. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Session Storage Performance** | System MUST define preview page performance requirements when using session storage (sessionStorage read/write overhead). Session storage operations MUST be fast. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Network Performance** | System MUST define preview page performance requirements under different network conditions (slow network, offline mode). Preview page MUST work under various network conditions. | See Consolidation Summary | 3 | ✅ Applied |
| **Preview Performance Consistency** | System MUST define preview page performance consistency requirements (should performance match authenticated settings page). Preview performance MUST be consistent with authenticated experience. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Acceptance Criteria Operations** | System MUST define performance acceptance criteria for different operations (theme change, page load, validation). Each operation MUST have defined performance criteria. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Acceptance Criteria Conditions** | System MUST define performance acceptance criteria under different conditions (normal load, high load). Performance criteria MUST be defined for different load conditions. | See Consolidation Summary | 3 | ✅ Applied |
| **Performance Regression Acceptance** | System MUST define performance regression acceptance requirements (how much performance degradation is acceptable). Performance regressions MUST have defined thresholds. | **FR-120** | 3 | ✅ Applied |

---

## Consolidation Complete

**All 171 gap items have been consolidated into 60 new/updated requirements and applied to `spec.md`.**

See "Consolidation Summary" section above for complete mapping of original gap items to consolidated requirements.
