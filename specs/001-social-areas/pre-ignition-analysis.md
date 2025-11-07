Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Pre-Ignition Analysis: Social Areas Provisioning Phase 1

<details>
<summary>Expand for Table of Contents</summary>

- [Pre-Ignition Analysis: Social Areas Provisioning Phase 1](#pre-ignition-analysis-social-areas-provisioning-phase-1)
  - [1. Purpose](#1-purpose)
  - [2. Sources Reviewed](#2-sources-reviewed)
  - [3. Readiness Summary](#3-readiness-summary)
  - [4. Detailed Findings \& Recommendations](#4-detailed-findings--recommendations)
    - [4.1. F1. Runtime \& Package Baseline](#41-f1-runtime--package-baseline)
    - [4.2. F2. Service Provider Registration](#42-f2-service-provider-registration)
    - [4.3. F3. Queue \& Horizon Configuration](#43-f3-queue--horizon-configuration)
    - [4.4. F4. Metrics \& Logging Dependencies](#44-f4-metrics--logging-dependencies)
    - [4.5. F5. Database Schema Prep](#45-f5-database-schema-prep)
    - [4.6. F6. Automation Scripts](#46-f6-automation-scripts)
    - [4.7. F7. Documentation Drift](#47-f7-documentation-drift)
    - [4.8. F8. Observability \& Alerting](#48-f8-observability--alerting)
  - [5. Recommended Sequence Before Phase 3 Starts](#5-recommended-sequence-before-phase-3-starts)
  - [6. Tracking \& Ownership](#6-tracking--ownership)
  - [7. Validation Criteria for “Ready to Start Phase 3”](#7-validation-criteria-for-ready-to-start-phase-3)

</details>

---

## 1. Purpose

- Confirm whether the current Storefront environment can immediately begin executing `tasks.md`
- Surface configuration, tooling, and dependency gaps before writing any feature code
- Recommend prioritized remediation steps so Phase 1 work starts from a stable platform

## 2. Sources Reviewed

- `composer.json`, `package.json`
- `.env.example`
- `config/*.php` (queues, providers)
- `bootstrap/providers.php`
- `database/migrations/` and `database/seeders/`
- Feature artifacts in `specs/001-social-areas/`

## 3. Readiness Summary

| Dimension | Readiness | Confidence |
| --- | --- | --- |
| Tooling & Runtime | 🔴 Blocked | 70% – Bun/Node ≥25 and Redis/PostgreSQL dependencies unmet locally |
| Configuration Baseline | 🔴 Blocked | 85% – Missing env vars, queue channels, provider wiring |
| Data Model & Seeding | 🔴 Blocked | 90% – No Social Areas migrations/seeders registered |
| Observability & Ops | 🟠 At Risk | 60% – Horizon not configured; alerts absent |
| Documentation & Automation | 🟠 At Risk | 65% – Quickstart references commands that do not yet exist |

**Overall Readiness**: ❌ Not ready. Phase 1 tasks require foundational environment work before implementation can start.

## 4. Detailed Findings & Recommendations

### 4.1. F1. Runtime & Package Baseline

- **Finding**: `.env.example` still targets `DB_CONNECTION=sqlite`, `QUEUE_CONNECTION=database`, and `MAIL_MAILER=log`. Feature requires PostgreSQL, Redis-backed queues, and transactional mail.
- **Impact**: Tests for Sanctum/Fortify auth, queue workers, and SLA metrics cannot execute meaningfully.
- **Priority**: P0 (blocker)
- **Action**: Extend `.env.example`/`.env.testing` with production-like defaults (PostgreSQL DSN, Redis queue hosts, `SOCIAL_AREAS_EXPIRY_MINUTES`, mail credentials). Aligns with `T001`–`T003` and `T007`.

### 4.2. F2. Service Provider Registration

- **Finding**: `bootstrap/providers.php` lacks `SocialAreasServiceProvider`; directory has only base providers.
- **Impact**: Policies, listeners, bindings, and route middleware from future tasks cannot register.
- **Priority**: P0
- **Action**: Scaffold `app/Providers/SocialAreasServiceProvider.php` and register it (per `T003`). Include stub bindings for queues, jobs, observers to unblock iterative work.

### 4.3. F3. Queue & Horizon Configuration

- **Finding**: `config/queue.php` does not declare `invitations` or `audits` channels. `config/horizon.php` missing entirely.
- **Impact**: Queue workers cannot target story-specific queues; Horizon dashboard/alerts cannot be defined.
- **Priority**: P0
- **Action**: Introduce queue channel configuration tied to new env vars (`QUEUE_INVITATIONS`, `QUEUE_AUDITS`). Add `config/horizon.php` with dashboard metrics and concurrency aligned to spec. Supports `T002`, `T007`, `T057`.

### 4.4. F4. Metrics & Logging Dependencies

- **Finding**: No Prometheus client config or structured logging helper exists; `LOG_CHANNEL` remains `stack` without JSON configuration.
- **Impact**: Fails non-functional requirements (structured JSON logs with `trace_id`, SLA metric emission).
- **Priority**: P1
- **Action**: Plan early addition of logging formatter and metrics exporter before integration tests (`T055`, `T059`). Document instrumentation steps in `docs/010-setup/090-observability.adoc` (ties to `T049`, `T056`).

### 4.5. F5. Database Schema Prep

- **Finding**: `database/migrations/` contains only default scaffolding; no Social Areas tables; `database/seeders/` lacks feature seeders.
- **Impact**: Cannot run gradient tests or `quickstart` flows; tasks referencing migrations/seeders will fail until scaffolding exists.
- **Priority**: P0
- **Action**: Execute Phase 1 & 2 migrations/seeders before touching story code (`T004`–`T020`, `T051`). Ensure timestamp ordering matches tasks.

### 4.6. F6. Automation Scripts

- **Finding**: `composer setup` script invokes `bun install` but environment requires Bun ≥1.1. No guardrails verifying Bun/Redis/Postgres presence.
- **Impact**: Team members may start with incomplete toolchain leading to inconsistent results.
- **Priority**: P1
- **Action**: Add preflight script (e.g., extend `.specify` or `bin/check-env`) to assert required binaries/versions, or document manual verification in quickstart.

### 4.7. F7. Documentation Drift

- **Finding**: `quickstart.md` references `social-areas:seed-demo` and queue names that do not exist yet.
- **Impact**: Confusion for new developers; instructions fail when executed today.
- **Priority**: P1
- **Action**: Annotate quickstart with “pending implementation” notes or gate behind `T047`/`T051` completion; ensure doc updates happen immediately after code lands.

### 4.8. F8. Observability & Alerting

- **Finding**: No baseline for Prometheus scraping, Grafana dashboards, or alert rules. Spec expects SLA alerts and degradation banner triggers.
- **Impact**: Post-implementation monitoring would be reactive, risking SLA breaches.
- **Priority**: P1
- **Action**: Plan instrumentation setup parallel with Phase 4 tasks. Consider provisioning placeholder dashboard JSON or IaC to accelerate `T057`–`T058`.

## 5. Recommended Sequence Before Phase 3 Starts

1. **Environment Variables & Config**: Implement `T001`–`T003`, switch default storage to Postgres/Redis, commit sample values.
2. **Provider & Queue Scaffolding**: Complete `T003` & `T007`; introduce queue names and Horizon config.
3. **Baseline Migrations/Seeders**: Deliver `T004`–`T020`, `T051`; verify `php artisan migrate` succeeds on PostgreSQL.
4. **Toolchain Verification**: Create short script/checklist to ensure PHP 8.4.12, Bun ≥1.1, Redis, PostgreSQL reachable before QA starts.
5. **Documentation Sync**: Update quickstart and ops docs once scaffolding is in place to prevent misinformation.

## 6. Tracking & Ownership

- Record remediation tasks against existing IDs in `tasks.md` to avoid duplication.
- Use Constitution gate: do not proceed to Phase 3 feature work until F1–F3 findings are resolved and validated via environment smoke checks.

## 7. Validation Criteria for “Ready to Start Phase 3”

- `php artisan config:cache` and `php artisan queue:listen invitations` succeed referencing Redis queue.
- `php artisan migrate:fresh --seed` runs against PostgreSQL with new tables.
- `php artisan social-areas:seed-demo` (or temporary equivalent) executes without errors.
- Horizon dashboard displays `invitations` & `audits` queues with workers attached.
- Sample request via `POST /api/v1/rooms` (stub) logs JSON output with `trace_id` placeholder, confirming logging format.

Once the above signals are green, the environment can safely enter Phase 3 implementation.
