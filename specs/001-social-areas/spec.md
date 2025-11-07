# Feature Specification: Social Areas Provisioning Phase 1
Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

<details>
<summary>Expand for Table of Contents</summary>

- [Feature Specification: Social Areas Provisioning Phase 1](#feature-specification-social-areas-provisioning-phase-1)
  - [1. Clarifications](#1-clarifications)
    - [1.1. Session 2025-11-07](#11-session-2025-11-07)
  - [2. User Scenarios \& Testing *(mandatory)*](#2-user-scenarios--testing-mandatory)
    - [2.1. User Story 1 - Resident Defines Private \& Shared Rooms (Priority: P1)](#21-user-story-1---resident-defines-private--shared-rooms-priority-p1)
    - [2.2. User Story 2 - Residents Host Guests in the Greenroom (Priority: P2)](#22-user-story-2---residents-host-guests-in-the-greenroom-priority-p2)
    - [2.3. User Story 3 - Public Visitors Access the Lobby (Priority: P3)](#23-user-story-3---public-visitors-access-the-lobby-priority-p3)
    - [2.4. Edge Cases](#24-edge-cases)
  - [3. Requirements *(mandatory)*](#3-requirements-mandatory)
    - [3.1. Functional Requirements](#31-functional-requirements)
    - [3.2. Key Entities *(include if feature involves data)*](#32-key-entities-include-if-feature-involves-data)
    - [3.3. Assumptions](#33-assumptions)
  - [4. Success Criteria *(mandatory)*](#4-success-criteria-mandatory)
    - [4.1. Measurable Outcomes](#41-measurable-outcomes)
  - [5. Non-Functional Requirements](#5-non-functional-requirements)
    - [5.1. Security \& Privacy](#51-security--privacy)
    - [5.2. Observability \& Alerts](#52-observability--alerts)
    - [5.3. Performance \& Capacity](#53-performance--capacity)
    - [5.4. Data Integrity \& Lifecycle](#54-data-integrity--lifecycle)
  - [6. Architectural \& Implementation Notes](#6-architectural--implementation-notes)
  - [7. Implementation Process Expectations](#7-implementation-process-expectations)
  - [8. Environment Setup Summary](#8-environment-setup-summary)

</details>

---

**Feature Branch**: `001-social-areas`
**Created**: 2025-11-07
**Status**: Draft
**Input**: User description: "social & collaboration application:phase 1 ..."

## 1. Clarifications

### 1.1. Session 2025-11-07

- Q: How should the system handle concurrent host approvals if multiple residents send invitations to the same guest? → A: Allow guests to hold multiple active invitations, each tied to its issuing resident’s rooms.
- Q: Where must access log entries be stored for audit purposes in Phase 1? → A: Persist in primary relational database audit tables.
- Q: What is the expiration policy for resident-issued guest invitations in Phase 1? → A: 72 hours with notifications to guest and host on expiry.
- Q: How should expiry notifications reach hosts and guests for lapsed invitations? → A: Email plus in-app alerts to both parties.
- Q: How long must audit log entries be retained in Phase 1? → A: Retain for 90 days with scheduled purge.
- Q: Which service should deliver expiry emails in Phase 1? → A: Existing transactional email provider via Laravel Mail queue.
- Q: How should invited guests authenticate for greenroom access in Phase 1? → A: Magic-link creates/activates guest account tied to email.
- Q: How should lobby invitation requests be throttled to deter abuse? → A: Cap at 3 submissions per email/IP per 24 hours, queue extras for review.

## 2. User Scenarios & Testing *(mandatory)*

### 2.1. User Story 1 - Resident Defines Private & Shared Rooms (Priority: P1)

A resident sets up their sanctuary, parlour, and den rooms, choosing who can enter each space before inviting others.

**Why this priority**: Establishing personal room access is the foundation for meaningful collaboration and privacy within the residence area.

**Independent Test**: Create a resident account, configure room permissions, and verify access by attempting entry with different user roles.

**Acceptance Scenarios**:

1. **Given** a resident with default room settings, **When** they review sanctuary access, **Then** only the resident can enter.
2. **Given** a resident toggles parlour sharing to include selected guests, **When** an invited guest attempts entry, **Then** the guest is admitted and non-invited users are denied.

---

### 2.2. User Story 2 - Residents Host Guests in the Greenroom (Priority: P2)

Residents greet invited guests in the greenroom lobby, acting as hosts before escorting them to shared rooms.

**Why this priority**: The greenroom is the transitional space that enables moderated guest access without exposing private areas prematurely.

**Independent Test**: Create resident and guest accounts, issue an invitation, and confirm host-led guest entry into the greenroom while unauthorized users are blocked.

**Acceptance Scenarios**:

1. **Given** a guest invitation issued by a resident host, **When** the guest joins the greenroom, **Then** the resident host is notified and can approve access.
2. **Given** a user without an invitation, **When** they attempt to enter the greenroom, **Then** the system denies entry and directs them to the public lobby.
3. **Given** an invited guest opens the magic-link invitation, **When** they authenticate, **Then** the system provisions/activates a guest account and issues credentials for greenroom access.

---

### 2.3. User Story 3 - Public Visitors Access the Lobby (Priority: P3)

Public visitors and unauthenticated users access the lobby to view community updates and request guest invitations.

**Why this priority**: A welcoming public lobby introduces the experience and feeds the invitation funnel without requiring immediate registration.

**Independent Test**: Visit the lobby as a guest, verify availability of public content, and confirm the invitation request workflow works end-to-end.

**Acceptance Scenarios**:

1. **Given** a public visitor on the lobby page, **When** they browse available announcements, **Then** content loads without requiring authentication.
2. **Given** a public visitor submits an invitation request, **When** a resident reviews it, **Then** the resident can approve or decline and the visitor receives the appropriate response.
3. **Given** a public visitor submits more than three requests within 24 hours, **When** the limit is exceeded, **Then** the system queues the extra submissions for review and informs the visitor of the throttling window.

---

### 2.4. Edge Cases

- What happens when a resident attempts to share the sanctuary? (System MUST disallow any access grants beyond the resident.)
- How does the system handle a guest attempting to enter the den without resident status? (Entry denied with guidance to request resident privileges.)
- Guests may hold multiple active invitations simultaneously, with access scoped to each issuing resident’s rooms to avoid approval conflicts.
- Expired invitations lapse after 72 hours with combined email (sent via the existing transactional provider and Mail queue) and in-app notifications to both guest and host, prompting re-request if needed.

## 3. Requirements *(mandatory)*

### 3.1. Functional Requirements

- **FR-001**: System MUST provision three top-level areas—lobby, greenroom, residence—with distinct access policies.
- **FR-002**: System MUST enforce role-based permissions for public visitors, guests, and residents across all areas.
- **FR-003**: Residents MUST be able to configure sharing settings for sanctuary (private), parlour (optional residents/guests), and den (residents only).
- **FR-004**: Residents MUST be able to invite, approve, or revoke guest access to the greenroom and parlour.
- **FR-005**: System MUST provide a public invitation request flow surfaced in the lobby.
- **FR-006**: System MUST log access attempts (successful and denied) in dedicated relational audit tables for review with automated 90-day retention.
- **FR-007**: System MUST present contextual messaging when access is denied, guiding users to the appropriate next step.
- **FR-008**: All email notifications (invites, expiries, approvals) MUST be dispatched through the existing transactional email provider using the Laravel Mail queue.
- **FR-009**: Successful guest entry MUST provision or activate a guest user account via single-use magic link tied to the invitation email, issuing a Sanctum token for session enforcement.
- **FR-010**: Lobby invitation requests MUST enforce a 3 submissions per email/IP per 24-hour limit, with excess requests persisted for host review and accompanied by rate-limit messaging.

### 3.2. Key Entities *(include if feature involves data)*

- **Area**: Represents lobby, greenroom, or residence; tracks access rules and display content.
- **Room**: Sanctuary, parlour, or den objects linked to a specific resident, including configurable sharing state.
- **User Role**: Public visitor, guest, resident; determines baseline access privileges.
- **Invitation**: Records the relationship between host residents and prospective guests, including status and 72-hour expiry with email plus in-app expiry notifications.
- **Access Log Entry**: Captures attempts to enter areas/rooms with actor, timestamp, and outcome within the primary relational audit tables with 90-day retention and scheduled purge.

### 3.3. Assumptions

- "Greenm room" refers to a "greenroom" hosting space; implemented as such.
- Residents are verified members with the ability to host and manage guests.
- Phase 1 focuses on access provisioning, not on real-time collaboration features inside rooms.

## 4. Success Criteria *(mandatory)*

### 4.1. Measurable Outcomes

- **SC-001**: 100% of sanctuary access attempts by non-resident actors are blocked and logged.
- **SC-002**: 95% of invited guests can enter the greenroom within 30 seconds of host approval.
- **SC-003**: At least 80% of residents configure parlour sharing settings during onboarding walkthrough testing.
- **SC-004**: Invitation request responses (approve or decline) are delivered within 5 minutes for 95% of submissions.

## 5. Non-Functional Requirements

### 5.1. Security & Privacy

- **Role definitions**: Public visitors may browse lobby content only; guests may enter the greenroom and parlour once a resident host approves; residents govern sanctuary, parlour, and den access; no emergency override flows are provided in Phase 1 (out of scope).
- **Least privilege enforcement**: Sanctuary and den remain resident-only. Parlour access is explicitly granted or revoked per guest/resident by the owning resident and must default to resident-only.
- **Magic-link guarantees**: Invitation emails deliver a single-use, 72-hour magic link that provisions or activates a guest account, invalidates other pending links for that invitation, and issues a Sanctum token. Tokens are stored hashed and may not surface in plaintext logs or messages.
- **Notification security**: Email notifications exclude sensitive data (no raw tokens or audit payloads), require TLS or the project’s configured secure transport, and advise recipients to report suspicious links.
- **Audit scope**: Every access attempt log captures actor identifier (or anonymous marker), role at the time of access, target type/id, outcome, human-readable message, and the correlation/trace identifier that triggered it. Only the owning resident and authorized operators can retrieve these records.
- **Assumptions**: Residents are verified upstream. Monitoring for compromised invitations or accounts must escalate to operations; incident handling steps are documented under Observability below.

### 5.2. Observability & Alerts

- **Structured logging**: All controllers, jobs, and listeners emit JSON logs containing `trace_id`, `actor_role`, `target_type`, `target_id`, `outcome`, and severity (`info` for successful flow, `warn` for throttled/denied access, `error` for unexpected failures).
- **Correlation propagation**: HTTP requests issue/propagate W3C Trace Context IDs into queue jobs and notifications so access logs, metrics, and mail events can be cross-referenced.
- **Metrics**: At minimum, record invitation approval latency, guest-entry latency, queue backlog depth, rate-limit violation counts, and purge job duration. Metrics must map directly to SC-002 and SC-004 thresholds.
- **Dashboards & alerting**: Horizon (or equivalent) dashboards track `invitations` and `audits` queues; Prometheus/Grafana (or equivalent) dashboards visualize the metrics above. Alerts trigger when (a) greenroom entry latency exceeds 30 seconds for 3 consecutive minutes, (b) invitation responses exceed 5 minutes for more than five requests, or (c) purge jobs fail or are skipped.
- **Operational runbook**: Operators review access logs daily, investigate alert events, and follow the incident-response steps documented with the queue monitoring playbook (see Implementation Process Expectations).

### 5.3. Performance & Capacity

- **Latency targets**: API endpoints exposed for social areas must maintain P95 < 500 ms. Greenroom entry must provide credentials within 30 seconds of host approval. Denial messaging must be rendered within 250 ms to prevent slow fail states.
- **Throughput**: The platform must support 10k residents, 50k guests, and solicitation bursts of 200 invitation approvals per hour. Queues must sustain ≥1,000 jobs/minute; rate-limiting stores must prevent drift without blocking legitimate traffic.
- **Degradation behaviour**: When SLA thresholds breach, the system surfaces a user-facing status banner, records an alert event, and queues a notification for operations. Lobby throttling responses include a retry-after timestamp.
- **External dependencies**: If the mail provider is unavailable, invitations queue safely and hosts receive an in-app notice. Backlogged jobs trigger alerts and remain retryable.

### 5.4. Data Integrity & Lifecycle

- **Schema guarantees**: Resident rooms are unique per resident/type; room permissions reference either a resident or guest email and cascade delete when a room is removed. Invitation uniqueness is enforced per host+guest while in a non-terminal state. Access logs use UUID primary keys and never update after write.
- **State transitions**: Invitation lifecycle states (pending → approved/expired/revoked) follow the defined transitions only; reopened invitations require a new issuance. Magic-link provisioning is idempotent and prevents duplicate user records.
- **Retention rules**: Purge jobs remove access logs older than 90 days while logging deletions, verifying counts, and skipping records newer than that threshold. Rate-limit counters roll over daily to prevent unbounded growth.
- **Atomic writes**: Access logs and invitation state changes commit atomically to avoid mismatched records; failures roll back both operations and emit an error log.

## 6. Architectural & Implementation Notes

- **Service boundary**: Phase 1 executes entirely within the Storefront service. No cross-database queries into CRM, ERP, or other domains are permitted.
- **Routing & policies**: HTTP APIs live under `/api/v1/social-areas/*` routes and rely on dedicated controllers plus policies per resource (`RoomPolicy`, `InvitationPolicy`). Policies enforce role-based access for every protected endpoint.
- **Events & queues**: Resident registration fires a listener that seeds default sanctuary/parlour/den rooms. Invitation approval/revocation and expiry emit events for notifications and audit logging. Invitation expiry and access-log purge run as scheduled queue jobs (`invitations`, `audits`) with consistent naming and cadences (hourly expiry, nightly purge) and must be idempotent/retry-safe.
- **Configuration**: Environment variables include `SOCIAL_AREAS_EXPIRY_MINUTES`, queue bindings, and mail provider settings. `SocialAreasServiceProvider` registers policies, listeners, schedule bindings, and configuration defaults.
- **Integration assumptions**: The existing transactional mail provider handles delivery, Sanctum/Fortify manage authentication boundaries, and Horizon monitors queue health. Queue failure handling (retry/backoff) and alerting are implemented via the observability requirements above. Any future emergency access tooling is explicitly out of scope for Phase 1.

## 7. Implementation Process Expectations

- **Prerequisite gates**: Complete environment setup, clarify outstanding questions via `/speckit.clarify`, review the architecture/security/performance checklists, and record open risks/decisions before coding begins.
- **Sequencing**: Follow the ordered phases in `tasks.md`—Setup → Foundational → User Stories → Polish—with [P] tasks executed only when their dependencies land.
- **TDD mandate**: For every story, author failing feature tests, unit tests, and contract tests before implementation. Demonstrate the red state, implement the minimal change to pass, and refactor with the suite green.
- **Quality gates**: Prior to merge, run Pint, PHPStan (max level), the targeted story test suites, and the full pipeline (`php artisan test --group=social-areas`, scheduled jobs, asset build). Update quickstart and observability docs once validation passes.
- **Operational follow-up**: After deployment, verify dashboards, queue depth, audit retention, and seeded demo data. Clear or adjust demo seeds that could violate production constraints. Maintain rollback procedures for migrations/jobs and track unresolved checklist items, risks, or clarifications as tasks for the next iteration.

## 8. Environment Setup Summary

- **Tooling**: PHP 8.4.12 with required extensions (per `composer.json`), Node/Bun tooling for assets, Redis for queues/cache, and PostgreSQL for Storefront schema. Sanctum/Fortify must be enabled for guest provisioning.
- **Configuration**: Define `SOCIAL_AREAS_EXPIRY_MINUTES` (default 4320), queue names (`invitations`, `audits`), mail provider credentials, and observability endpoints. Register `SocialAreasServiceProvider` to bind policies, listeners, schedules, and configuration defaults.
- **Queue & Horizon**: Configure Horizon to monitor the new queues with documented concurrency. Verify queue workers connect successfully and emit metrics following setup.
- **Seeding & Migrations**: Run social-area migrations in order (areas → rooms → room_permissions → invitations → access_logs → lobby requests). Use `social-areas:seed-demo --resident-email=...` for demo data while ensuring seeds respect production uniqueness rules. Provide rollback instructions for migrations/seeds.
- **Verification**: After setup, execute smoke tests—resident login, queue job execution, mail delivery via local trap, metrics/log emission, and targeted Artisan/Pest suites (e.g., `php artisan test --group=social-areas`).
- **Documentation**: Keep quickstart and operational runbooks updated with environment prerequisites, version history, and sync guidance between local/staging environments.
