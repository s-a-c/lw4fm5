# Implementation Plan: Social Areas Provisioning Phase 1

<details>
<summary>Expand for Table of Contents</summary>

- [Implementation Plan: Social Areas Provisioning Phase 1](#implementation-plan-social-areas-provisioning-phase-1)
  - [1. Summary](#1-summary)
  - [2. Technical Context](#2-technical-context)
  - [3. Constitution Check](#3-constitution-check)
  - [4. Project Structure](#4-project-structure)
    - [4.1. Documentation (this feature)](#41-documentation-this-feature)
    - [4.2. Source Code (repository root)](#42-source-code-repository-root)
  - [5. Complexity Tracking](#5-complexity-tracking)
  - [6. Phase 0 – Research Summary](#6-phase-0--research-summary)
  - [7. Phase 1 – Design \& Contracts](#7-phase-1--design--contracts)
    - [7.1. Data Model Overview (see `data-model.md`)](#71-data-model-overview-see-data-modelmd)
    - [7.2. API \& Interaction Contracts (stored under `contracts/`)](#72-api--interaction-contracts-stored-under-contracts)
    - [7.3. Operational Design](#73-operational-design)
    - [7.4. Testing \& Tooling](#74-testing--tooling)
  - [8. Implementation Process \& Environment Alignment](#8-implementation-process--environment-alignment)
  - [9. Constitution Check (Post-Design)](#9-constitution-check-post-design)

</details>

---

**Branch**: `005-social-areas` | **Date**: 2025-11-07 | **Spec**: [`spec.md`](./spec.md)
**Input**: Feature specification from `/specs/005-social-areas/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## 1. Summary

Phase 1 provisions three social areas (lobby, greenroom, residence) with resident-managed rooms, invitation gating, and audit-ready access logging. We will implement Laravel authorization policies, a state-driven invitation workflow with 72-hour expiry, and relational audit tables retained for 90 days while reusing the existing transactional mail provider for notifications. Non-functional scope now also codifies structured security controls (least privilege policies, hashed tokens), observability requirements (structured JSON logs, Prometheus metrics, Horizon monitoring), performance SLAs (API P95 < 500 ms, 30 s greenroom entry), and data integrity guarantees (idempotent jobs, atomic access logging, purge safeguards).

## 2. Technical Context

<!--
  ACTION REQUIRED: Replace the content in this section with the technical details
  for the project. The structure here is presented in advisory capacity to guide
  the iteration process.
-->

**Language/Version**: PHP 8.4.12 with strict types (per `composer.json`), including required extensions
**Primary Dependencies**: Laravel 12, Sanctum, Fortify, Laravel Mail queue, Horizon, Prometheus/Grafana (or equivalent) instrumentation, Pest 4
**Storage**: PostgreSQL (service-owned), Redis queues/cache, S3-compatible object storage for backups (existing)
**Testing**: Pest 4 (feature, unit, architecture suites) with Larastan level max
**Target Platform**: Storefront Laravel web service (API + Blade/Livewire)
**Project Type**: Web application within multi-service architecture (Storefront service)
**Performance Goals**: API P95 <500 ms, storefront interactions <1 s, queue throughput ≥1k jobs/min (constitution §6), greenroom entry credentials delivered within 30 s of approval
**Constraints**: Enforce Sanctum tokens, CSRF, RBAC via policies, structured JSON logs with correlation IDs, secure notifications (no plain tokens), 90-day audit retention, idempotent queue jobs (`invitations`, `audits`), single-use magic links
**Scale/Scope**: Launch support for 10k residents, 50k guests, peak 200 concurrent invitations/hour

## 3. Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- ✅ Policy acknowledgement header present on all deliverables (`Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e`)
- ✅ Test plan will precede implementation with red-green-refactor cycles documented in tasks
- ✅ Solution remains within Storefront service boundaries and interfaces with other services only via existing APIs
- ✅ Security baselines covered via Sanctum-auth API routes, form requests, MFA-capable auth, and RBAC policies
- ✅ Observability: structured JSON logs, W3C trace context propagation, Prometheus metrics for invitation and access flows
- ✅ Performance targets tracked with queue depth alerts and load tests ensuring P95 <500 ms for access APIs

## 4. Project Structure

### 4.1. Documentation (this feature)

```text
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### 4.2. Source Code (repository root)
<!--
  ACTION REQUIRED: Replace the placeholder tree below with the concrete layout
  for this feature. Delete unused options and expand the chosen structure with
  real paths (e.g., apps/admin, packages/something). The delivered plan must
  not include Option labels.
-->

```text
app/
├── Http/
│   └── Controllers/
├── Livewire/
├── Models/
└── Providers/

database/
├── migrations/
├── factories/
└── seeders/

resources/
├── views/
├── css/
└── js/

routes/
├── api.php
├── web.php
└── console.php

tests/
├── Feature/
├── Unit/
└── Browser/
```

**Structure Decision**: Extend the existing Storefront Laravel application, adding domain-specific models, policies, migrations, controllers, Livewire/Vilt components, and Pest tests within the established directories above.

## 5. Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| _None_ | — | — |

## 6. Phase 0 – Research Summary

- **Access control enforcement**: Confirmed Laravel authorization policies with Sanctum-authenticated requests satisfy FR-001/FR-003 without introducing new packages.
- **Audit logging strategy**: Settled on an `access_logs` table with queue-driven 90-day purge job plus structured logging for observability.
- **Invitation lifecycle**: Adopted a state-machine model with 72-hour expiry, queued mail delivery via existing provider, and in-app notifications for both hosts and guests.

Full details recorded in [`research.md`](./research.md).

## 7. Phase 1 – Design & Contracts

### 7.1. Data Model Overview (see [`data-model.md`](./data-model.md))

- `areas`: seed records (`lobby`, `greenroom`, `residence`) with access policy metadata.
- `rooms`: resident-scoped sanctuary/parlour/den definitions with sharing configuration and timestamps.
- `room_permissions`: join table storing guest/resident access overrides for parlour.
- `invitations`: state, issuer, invitee email, expiry timestamp, approval metadata, audit fields.
- `access_logs`: normalized audit table capturing actor, target (area/room), outcome, message, correlation ID, purged after 90 days.

### 7.2. API & Interaction Contracts (stored under [`contracts/`](./contracts/))

- `POST /api/v1/invitations`: create resident invitation (pending state, queued email).
- `POST /api/v1/invitations/{invitation}/approve`: host approval, triggers guest notification.
- `POST /api/v1/invitations/{invitation}/revoke`: mark revoked, emit alerts.
- `POST /api/v1/greenroom/entries`: guest entry request, checks invitation + logs outcome.
- `GET /api/v1/rooms`: resident room configuration (authorization required).
- `PUT /api/v1/rooms/{room}`: update parlour sharing settings.
- `GET /api/v1/lobby/content`: public lobby content feed.
- `POST /api/v1/lobby/invitation-request`: submit public request (notifies resident queue).

Contracts include request/response schemas, validation rules, and error payloads mapped to form requests and policies.

### 7.3. Operational Design

- Scheduled queue worker scans invitations hourly to expire and notify; nightly job purges `access_logs` older than 90 days. Jobs must be idempotent, emit success/failure logs, and raise alerts on repeated failures.
- Observability: controllers, jobs, and listeners emit structured JSON logs containing `trace_id`, actor role, target type/id, outcome, and severity (`info`, `warn`, `error`). Metrics exported to Prometheus/Grafana (or equivalent) capture invitation approval latency, guest-entry latency, queue backlog depth, rate-limit violations, and purge durations. Horizon monitors the `invitations` and `audits` queues with configured concurrency.
- Alerting thresholds: notify operations when queue backlog or purge jobs fail, when invitation response exceeds 5 minutes for more than five requests, or when greenroom entry latency exceeds 30 s for 3 consecutive minutes. Operators follow the documented runbook for escalation.
- Security: Sanctum tokens for authenticated flows, CSRF for web, form requests for validation, policies for access checks, hashed invitation tokens, secure notification copy (no sensitive data).
- Performance safeguards: eager load resident rooms, index `invitations.expires_at`, `access_logs.actor_id`, use Redis rate limiter on invitation requests, and expose degradation behaviour (user-facing status banner, retry-after messaging) when SLAs breach.

### 7.4. Testing & Tooling

- Pest feature tests for each API endpoint, unit tests for invitation state machine, browser test covering lobby → invitation request, and contract tests validating OpenAPI responses.
- Architecture tests ensure policies and controllers remain within Storefront boundaries and constitutional constraints (no cross-service DB access).
- Feature tests assert denial messaging copy, throttling responses, and SLA instrumentation assertions (latency counters, rate-limit metrics).
- Fixtures/factories for `Area`, `Room`, `Invitation`, `AccessLog`, and lobby requests; demo seeding command for local validation while respecting production uniqueness constraints.
- Quickstart instructions captured in [`quickstart.md`](./quickstart.md) for setting up queues, horizon, seeding base data, verifying metrics/logs, and executing targeted `php artisan test --group=social-areas` smoke suites.

## 8. Implementation Process & Environment Alignment

- **Gating steps**: Complete environment setup (toolchain, env vars, service provider registration), review updated checklists (architecture, security, performance, observability, environment), document risks/decisions, and rerun `/speckit.clarify` when scope shifts.
- **Sequencing & TDD**: Follow tasks phases strictly (Setup → Foundational → Story Phases → Polish). Author failing tests before implementation, demonstrate red state, achieve green, and refactor with full suite (including Pint, PHPStan max level).
- **Quality gates**: Run targeted suites (`php artisan test --group=social-areas`, queue schedule runners, asset builds) before merge, update quickstart/observability docs, and ensure architecture tests (T048) enforce boundaries.
- **Operational follow-up**: After deployment, verify dashboards, queue health, audit retention, and seeded demo data. Maintain rollback procedures for migrations/jobs and track unresolved checklist items or risks for subsequent iterations.
- **Environment parity**: Maintain local/staging parity via documented env vars, queue configuration, and Prometheus/Horizon dashboards; keep quickstart version history current when dependencies change.

## 9. Constitution Check (Post-Design)

- ✅ Policy acknowledgement accounted for in all generated artifacts (plan, research, data model, quickstart, contracts).
- ✅ TDD readiness: plan aligns with constitution §2 via explicit testing strategy and factory coverage.
- ✅ Service boundaries retained; no cross-database access proposed.
- ✅ Security controls enumerated (Sanctum, CSRF, policies, MFA).
- ✅ Observability and metrics coverage specified for new jobs and APIs.
- ✅ Performance guardrails (indexes, queue throughput, expiry jobs) documented with mitigation steps.
