# Implementation Plan: Base Platform Foundation

**Branch**: `001-base-platform` | **Date**: 2025-11-07 | **Spec**: [`spec.md`](./spec.md)
**Input**: Feature specification from `/specs/001-base-platform/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## Summary

Establish a reliable engineering baseline by delivering dual-path local setup (containerized and native), enforcing Bun as the JavaScript runtime across local/CI tooling, codifying credential management for the current solo developer, and standardizing GitHub workflows with tiered quality gates (core checks on every push, mutation/browser suites nightly and pre-release). The plan introduces bootstrap/CI recovery playbooks, an automated policy-acknowledgement checksum monitor, and observability hooks so future product work can build on a consistent platform.

## Technical Context

**Language/Version**: PHP 8.5 (strict types) with Bun 1.1+ for JavaScript tooling
**Primary Dependencies**: Laravel 12, Sanctum, Horizon, Pest 4, Laravel Pint, Rector, Bun toolchain, Playwright, Prometheus/OpenTelemetry exporters
**Storage**: PostgreSQL (primary application data), Redis (queues/cache)
**Testing**: Pest 4 (feature, unit, browser), PHPStan max (Larastan), mutation testing via Infection, automated nightly heavy suites
**Target Platform**: Storefront Laravel web service deployed on containerized infrastructure with GitHub Actions CI/CD
**Project Type**: Web application (monolithic Laravel app with Livewire/Vite frontend)
**Performance Goals**: Bootstrap completes within 45 minutes locally; CI lint/test/browser workflows finish within 25 minutes P90 with <5% flake rate; queues sustain ≥1k jobs/min; automated profile validation confirms both native and container flows; smoke checks validate asset builds and queue health post-bootstrap
**Constraints**: Maintain ≥90% code coverage, enforce Bun runtime parity across local/CI, preserve policy acknowledgement headers (with automated checksum monitoring), provide published credential rotation/onboarding guides, ensure secrets stored in GitHub Actions + encrypted local `.env`, and run automated profile validation on a scheduled cadence
**Scale/Scope**: Support 10k+ contributors’ environments over time (currently solo developer), nightly heavy-suite coverage, dual development paths kept in parity via automated smoke tests

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- ✅ Policy acknowledgement header present on all deliverables (`Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e`)
- ✅ Test plan approved before implementation, failing tests written (Red) before code (Green), refactor with tests green
- ✅ Solution respects service boundaries (Storefront only) and avoids cross-database access
- ✅ Security baselines enforced (credential policy: GitHub Actions secrets + encrypted local `.env`, Sanctum tokens, form request validation, rate limiting for future APIs)
- ✅ Observability plan defined (structured logs, correlation IDs, nightly health checks, metrics for bootstrap success and CI reliability)
- ✅ Performance targets evaluated (45-minute bootstrap SLA, CI <25 minutes P90, queue throughput ≥1k jobs/min) with mitigation steps in plan

## Project Structure

### Documentation (this feature)

```text
specs/001-base-platform/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
app/
├── Console/
├── Http/
├── Livewire/
├── Models/
└── Providers/

bootstrap/
config/
database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
├── api.php
├── console.php
└── web.php

tests/
├── Architecture/
├── Browser/
├── Feature/
└── Unit/
```

**Structure Decision**: Extend the existing Storefront Laravel application; add scripts, configuration, and documentation under the current monolith directories, keeping tooling definitions in `composer.json`, `package.json`, GitHub workflows, and dedicated support classes (service providers, commands) inside `app/`.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| _None_ | — | — |

## Automation Ownership & Responsibilities

- **Artisan commands** (`RunPlatformBootstrap`, `RunParityCheck`, `ValidateEnvironmentProfiles`, `PolicyChecksumMonitor`, `DependencyReviewReport`) are owned by the Platform Engineering team; they maintain schedules, alerts, and post-launch refinements.
- **Shell scripts** (`scripts/platform/*`, `scripts/profile/*`, `scripts/automation/*`) are owned by Platform Engineering for local tooling, with onboarding documentation in `docs/base-platform/` kept synchronized with command behavior.
- **GitHub workflows** (tests, lint, browser, nightly heavy suites) are owned jointly by Platform Engineering and DevEx; DevEx monitors CI reliability, while Platform Engineering ensures workflow configuration remains aligned with spec requirements.
- Ownership handoffs after launch must be recorded in the changelog and acknowledged during sprint retrospectives.

## Operational Cadence & Monitoring

| Automation | Cadence | Responsible Artifact |
|------------|---------|-----------------------|
| Policy checksum monitor | Nightly + pre-release | `.github/workflows/tests.yml`, `bootstrap/app.php` |
| Environment profile validation | Weekly + pre-release | `.github/workflows/tests.yml`, `bootstrap/app.php` |
| Mutation/browser heavy suites | Nightly + release gate | `.github/workflows/nightly-heavy.yml` |
| Dependency review report | Monthly | `bootstrap/app.php`, `scripts/automation/dependency-review.sh` |

All cadence adjustments must be reflected in `plan.md`, `tasks.md` checkpoints, and the quickstart QA section.

## QA Gates & Handoffs

- **Phase entry readiness**: Each phase in `tasks.md` includes a checkpoint ensuring documentation, scripts, and migrations are prepared before implementation begins.
- **User story exit criteria**: Completion requires passing automated tests (unit/feature/architecture), generated documentation updates, and archived validation reports.
- **QA tooling access**: Quickstart now documents commands (`platform:bootstrap`, `platform:validate-profiles`, `policy:checksum-monitor`) and expected logs so QA can execute or verify automation independently.
- **Success criteria mapping**: SC-001–SC-005 map to bootstrap validation, CI performance metrics, dependency review outputs, checklist confirmations, and support-metric tracking, ensuring QA can attest to each outcome.

Support-request reduction (SC-005) will be measured via tagged helpdesk tickets; the process for capturing and reporting this metric is documented in `docs/base-platform/support-metrics.md` and referenced in the quickstart QA workflow.
