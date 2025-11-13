# Tasks: Social Areas Provisioning Phase 1

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

<details>
<summary>Expand for Table of Contents</summary>

- [Tasks: Social Areas Provisioning Phase 1](#tasks-social-areas-provisioning-phase-1)
  - [1. Phase 1: Setup (Shared Infrastructure)](#1-phase-1-setup-shared-infrastructure)
  - [2. Phase 2: Foundational (Blocking Prerequisites)](#2-phase-2-foundational-blocking-prerequisites)
  - [3. Phase 3: User Story 1 – Resident Defines Private \& Shared Rooms (Priority: P1) 🎯 MVP](#3-phase-3-user-story-1--resident-defines-private--shared-rooms-priority-p1--mvp)
    - [3.1. Tests for User Story 1 (write first)](#31-tests-for-user-story-1-write-first)
    - [3.2. Implementation for User Story 1](#32-implementation-for-user-story-1)
  - [4. Phase 4: User Story 2 – Residents Host Guests in the Greenroom (Priority: P2)](#4-phase-4-user-story-2--residents-host-guests-in-the-greenroom-priority-p2)
    - [4.1. Tests for User Story 2 (write first)](#41-tests-for-user-story-2-write-first)
    - [4.2. Implementation for User Story 2](#42-implementation-for-user-story-2)
  - [5. Phase 5: User Story 3 – Public Visitors Access the Lobby (Priority: P3)](#5-phase-5-user-story-3--public-visitors-access-the-lobby-priority-p3)
    - [5.1. Tests for User Story 3 (write first)](#51-tests-for-user-story-3-write-first)
    - [5.2. Implementation for User Story 3](#52-implementation-for-user-story-3)
  - [6. Phase 6: Polish \& Cross-Cutting Concerns](#6-phase-6-polish--cross-cutting-concerns)
  - [7. Dependencies \& Execution Order](#7-dependencies--execution-order)
  - [8. Parallel Execution Opportunities](#8-parallel-execution-opportunities)
  - [9. Implementation Strategy](#9-implementation-strategy)
    - [9.1. MVP First (User Story 1 Only)](#91-mvp-first-user-story-1-only)
    - [9.2. Incremental Delivery](#92-incremental-delivery)
    - [9.3. Team Parallelization](#93-team-parallelization)
  - [10. Notes](#10-notes)

</details>

---

**Input**: Design documents from `/specs/005-social-areas/`
**Prerequisites**: `plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/openapi.yaml`, `quickstart.md`

**Tests**: Tests are **MANDATORY**. Write failing tests first for every user story, then implement code to satisfy them per the constitution’s TDD mandate.

**Organization**: Tasks are grouped by user story to keep each increment independently testable and deployable.

## 1. Phase 1: Setup (Shared Infrastructure)

**Purpose**: Establish configuration scaffolding required by all stories.

- [ ] T001 Update `.env.example` and `.env.testing` with `SOCIAL_AREAS_EXPIRY_MINUTES=4320`
- [ ] T002 Create `config/social-areas.php` binding expiry, queue names, and notification channels to env defaults
- [ ] T003 Scaffold `app/Providers/SocialAreasServiceProvider.php` and register it in `bootstrap/providers.php`
- [ ] T051 [P] Create `app/Console/Commands/SocialAreasSeedDemo.php` to mirror quickstart seeding workflow and register it in `app/Console/Kernel.php`

---

## 2. Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before any user story work.

- [ ] T004 Create `database/migrations/2025_11_07_120000_create_areas_table.php` with seeded lobby/greenroom/residence rows
- [ ] T005 Add `database/seeders/SocialAreasSeeder.php` and register it inside `database/seeders/DatabaseSeeder.php`
- [ ] T006 Create `app/Models/Area.php` with relationships to rooms and access logs
- [ ] T007 Update `config/queue.php` and `config/horizon.php` to declare `invitations` and `audits` queues for Horizon monitoring
- [ ] T008 Add shared Pest base `tests/Feature/SocialAreas/SocialAreasFeatureTestCase.php` with Sanctum helpers and area seeding

**Checkpoint**: Foundation ready — user story implementation can now begin.

---

## 3. Phase 3: User Story 1 – Resident Defines Private & Shared Rooms (Priority: P1) 🎯 MVP

**Goal**: Residents manage sanctuary, parlour, and den access while enforcing role policies.

**Independent Test**: Resident toggles parlour sharing, sanctuary remains private, den restricted to residents; APIs return correct room metadata.

### 3.1. Tests for User Story 1 (write first)

- [ ] T009 [P] [US1] Create `tests/Feature/SocialAreas/Rooms/ManageRoomsTest.php` covering `GET /api/v1/rooms` and `PUT /api/v1/rooms/{room}` access rules

### 3.2. Implementation for User Story 1

- [ ] T010 [P] [US1] Add `database/migrations/2025_11_07_121000_create_rooms_table.php` defining resident rooms schema
- [ ] T011 [P] [US1] Add `database/migrations/2025_11_07_121100_create_room_permissions_table.php` with guest/resident overrides
- [ ] T012 [P] [US1] Create `app/Models/Room.php` with enum casts, relationships, and scope helpers
- [ ] T013 [P] [US1] Create `app/Models/RoomPermission.php` with subject polymorphism helpers
- [ ] T014 [P] [US1] Create `app/Http/Requests/SocialAreas/UpdateRoomRequest.php` enforcing parlour-only sharing changes
- [ ] T015 [P] [US1] Create `app/Http/Resources/SocialAreas/RoomResource.php` including permissions payload
- [ ] T016 [US1] Implement `app/Policies/RoomPolicy.php` and register it inside `SocialAreasServiceProvider`
- [ ] T017 [US1] Implement `app/Http/Controllers/SocialAreas/RoomController.php` for index/update actions with policy checks
- [ ] T018 [US1] Register room routes and Sanctum middleware under `/api/v1/rooms` in `routes/api.php`
- [ ] T019 [US1] Add `app/Listeners/SocialAreas/CreateDefaultRooms.php` and wire to resident registration events
- [ ] T020 [US1] Extend `SocialAreasSeeder` to backfill default rooms for existing residents and parlour permissions for invites
- [ ] T054 [US1] Extract shared denial messaging helper (e.g., `app/Support/SocialAreas/AccessDenied.php`) and update controllers/tests to assert contextual copy

**Checkpoint**: User Story 1 functional and independently testable.

---

## 4. Phase 4: User Story 2 – Residents Host Guests in the Greenroom (Priority: P2)

**Goal**: Residents issue invitations, approve guests, and log/notify access to the greenroom.

**Independent Test**: Invitation lifecycle (create → approve → revoke), magic-link provisioning, greenroom entry with audit logs and notifications.

### 4.1. Tests for User Story 2 (write first)

- [ ] T021 [P] [US2] Create `tests/Feature/SocialAreas/Invitations/InvitationLifecycleTest.php` covering POST/approve/revoke flows and notifications
- [ ] T022 [P] [US2] Create `tests/Feature/SocialAreas/Greenroom/GreenroomEntryTest.php` covering magic-link login, access logging, and denial paths

### 4.2. Implementation for User Story 2

- [ ] T023 [P] [US2] Add `database/migrations/2025_11_07_122000_create_invitations_table.php` with state, expiry, and token columns
- [ ] T024 [P] [US2] Add `database/migrations/2025_11_07_122100_create_access_logs_table.php` capturing actor, target, and outcome
- [ ] T025 [P] [US2] Create `app/Models/Invitation.php` with state machine methods and relations to host/guest/rooms
- [ ] T026 [P] [US2] Create `app/Models/AccessLog.php` and corresponding factory in `database/factories/AccessLogFactory.php`
- [ ] T027 [P] [US2] Create form requests in `app/Http/Requests/SocialAreas/CreateInvitationRequest.php` and `ApproveInvitationRequest.php`
- [ ] T028 [P] [US2] Create `app/Http/Resources/SocialAreas/InvitationResource.php` and transformer for audit payloads
- [ ] T029 [P] [US2] Create queued notifications in `app/Notifications/SocialAreas/InvitationCreated.php`, `InvitationApproved.php`, and `InvitationExpired.php`
- [ ] T030 [US2] Implement `app/Services/SocialAreas/InvitationService.php` orchestrating create/approve/revoke with policies and notifications
- [ ] T031 [US2] Implement `app/Services/SocialAreas/GreenroomEntryService.php` issuing magic-link guest accounts and writing access logs
- [ ] T032 [US2] Implement controllers `app/Http/Controllers/SocialAreas/InvitationController.php` and `GreenroomEntryController.php`
- [ ] T033 [US2] Register invitation and greenroom routes in `routes/api.php` with Sanctum + rate limiting
- [ ] T034 [US2] Create `app/Jobs/SocialAreas/ExpireInvitationsJob.php` and schedule it hourly in `routes/console.php`
- [ ] T035 [US2] Hook invitation events/listeners in `SocialAreasServiceProvider` to dispatch notifications and audits
- [ ] T036 [US2] Implement `app/Policies/InvitationPolicy.php` and bind to the provider for host-only actions
- [ ] T052 [US2] Implement `app/Jobs/SocialAreas/PurgeAccessLogsJob.php` deleting audit rows older than 90 days with structured logging
- [ ] T053 [US2] Schedule `PurgeAccessLogsJob` nightly in `routes/console.php` and cover retention with `tests/Feature/SocialAreas/AccessLogRetentionTest.php`
- [ ] T055 [US2] Add SLA timing metrics for invitation approval and greenroom entry (Prometheus counters/events) with assertions in invitation/greenroom feature tests

**Checkpoint**: User Stories 1 & 2 functional and independently testable.

---

## 5. Phase 5: User Story 3 – Public Visitors Access the Lobby (Priority: P3)

**Goal**: Public visitors read lobby announcements and submit invitation requests with throttling.

**Independent Test**: Anonymous visitor reads lobby content, submits invitation request, receives throttled response when spamming.

### 5.1. Tests for User Story 3 (write first)

- [ ] T037 [P] [US3] Create `tests/Feature/SocialAreas/Lobby/LobbyContentTest.php` verifying anonymous GET `/api/v1/lobby/content`
- [ ] T038 [P] [US3] Create `tests/Feature/SocialAreas/Lobby/LobbyInvitationRequestTest.php` covering POST `/api/v1/lobby/invitation-request` success, throttling, and resident notifications

### 5.2. Implementation for User Story 3

- [ ] T039 [P] [US3] Add `database/migrations/2025_11_07_123000_create_lobby_invitation_requests_table.php`
- [ ] T040 [P] [US3] Create `app/Models/LobbyInvitationRequest.php` and factory for test data
- [ ] T041 [P] [US3] Create `app/Http/Requests/SocialAreas/LobbyInvitationRequest.php` validating email/message payloads
- [ ] T042 [US3] Implement `app/Http/Controllers/SocialAreas/LobbyController.php` responding with announcements and queuing requests
- [ ] T043 [US3] Register lobby routes, anonymous guards, and rate limiter in `routes/api.php` and `bootstrap/app.php`
- [ ] T044 [US3] Create `app/Notifications/SocialAreas/LobbyInvitationSubmitted.php` to alert resident hosts
- [ ] T045 [US3] Extend `SocialAreasSeeder` to publish sample lobby announcements for acceptance testing
- [ ] T056 [US3] Document SLA monitoring setup in `docs/010-setup/090-observability.md`, highlighting queue timing dashboards for SC-002 and SC-004

**Checkpoint**: All user stories functional and independently testable.

---

## 6. Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Wrap-up, quality gates, and documentation.

- [ ] T046 [P] Refresh `contracts/openapi.yaml` and add contract tests in `tests/Feature/SocialAreas/OpenApiComplianceTest.php`
- [ ] T047 [P] Update `quickstart.md` verification steps after end-to-end run-through
- [ ] T048 Add architecture tests in `tests/Unit/Arch/SocialAreasArchitectureTest.php` enforcing policies and route namespaces
- [ ] T049 Capture queue + schedule monitoring playbook in `docs/010-setup/090-observability.md`
- [ ] T050 Execute full pipeline: `php artisan test --group=social-areas`, `php artisan schedule:run`, `bun run build`, and record results in task notes
- [ ] T057 Configure Prometheus/Horizon alert rules for SLA breaches (greenroom latency, invitation response time, queue backlog, purge failures) and document escalation steps
- [ ] T058 Implement user-facing degradation banner and retry-after messaging triggered when SLA alerts fire, including feature tests covering the fallback experience
- [ ] T059 Add logging severity helper/guidance ensuring controllers, jobs, and listeners emit `info`/`warn`/`error` according to spec, and update tests to assert severity usage where applicable

---

## 7. Dependencies & Execution Order

- **Setup (Phase 1)** → **Foundational (Phase 2)** → **User Story 1 (Phase 3)** → **User Story 2 (Phase 4)** → **User Story 3 (Phase 5)** → **Polish (Phase 6)**
- User Story phases require all preceding phases complete. After Phase 2, stories can proceed sequentially or in parallel by separate developers, provided cross-story dependencies (rooms before invitations) are respected.
- Foundational phase is a hard gate: no user story work until tasks T004–T008 are complete and verified.

## 8. Parallel Execution Opportunities

- Tasks marked **[P]** target distinct files and can run concurrently once their phase begins.
- Within User Story 1, T010–T015 can proceed in parallel after T009 exists; T016–T020 depend on those outputs.
- User Stories 2 and 3 can progress in parallel teams after completing Phase 3, using their own [P] tasks for migrations/models/tests.
- Contract/tests tasks (T009, T021–T022, T037–T038) may run simultaneously because they create separate test suites.

## 9. Implementation Strategy

### 9.1. MVP First (User Story 1 Only)

1. Complete Setup (T001–T003).
2. Complete Foundational (T004–T008).
3. Execute Phase 3 tasks T009–T020 with TDD.
4. Run acceptance checks from `quickstart.md` specific to rooms.
5. Demo MVP (resident room management) before expanding scope.

### 9.2. Incremental Delivery

1. Foundation through Phase 3 → deploy MVP.
2. Add User Story 2 (invitations & greenroom) → deploy once tests pass.
3. Add User Story 3 (lobby public funnel) → deploy after verification.
4. Finish with Polish phase to solidify documentation and observability.

### 9.3. Team Parallelization

1. Collaboratively finish Setup + Foundational.
2. Assign developers per story:
   - Dev A: User Story 1
   - Dev B: User Story 2
   - Dev C: User Story 3
3. Coordinate via shared test cases and architecture rules; merge sequentially respecting story priorities.

## 10. Notes

- Maintain ≥90% coverage and ensure tests fail before implementation per constitution §2.
- Register all new policies, listeners, and schedules in `SocialAreasServiceProvider` to centralize bindings.
- Keep migrations idempotent and timestamped in chronological order provided above.
- Use structured logging helpers when writing audit events (T031, T035).
- Update documentation and quickstart entries as code diverges to prevent drift.
