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

- ✅ Policy acknowledgement header present on all deliverables (`Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e`)
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

- **Artisan commands** (`platform:bootstrap`, `platform:parity-check`, `platform:validate-profiles`, `policy:checksum-monitor`, `platform:dependency-review`) are owned by the Platform Engineering team; they maintain schedules, alerts, and post-launch refinements. Command classes live in `app/Console/Commands/` (for example `RunPlatformBootstrap`, `RunParityCheck`, `ValidateEnvironmentProfiles`, `PolicyChecksumMonitor`, `DependencyReviewReport`).
- **Shell scripts** (`scripts/platform/*`, `scripts/profile/*`, `scripts/automation/*`) are owned by Platform Engineering for local tooling, with onboarding documentation in `docs/base-platform/` kept synchronized with command behavior.
- **GitHub workflows** (tests, lint, browser, nightly heavy suites) are owned jointly by Platform Engineering and DevEx; DevEx monitors CI reliability, while Platform Engineering ensures workflow configuration remains aligned with spec requirements.
- **Workflow suite channels** (`workflow_suite_channels` table and supporting models/services) are maintained by Platform Engineering to map each workflow suite to its Slack, email, or webhook destinations without duplicating suite metadata.
- Ownership handoffs after launch must be recorded in the changelog and acknowledged during sprint retrospectives.

### Component Responsibility Alignment

| Artifact Type | Runtime Entry Point | Responsibility | Primary References |
|---------------|---------------------|----------------|--------------------|
| Artisan command (`platform:bootstrap`) | `app/Console/Commands/RunPlatformBootstrap.php` | Execute bootstrap orchestration, dispatch smoke tests, emit metrics | Phase 3 T027, `quickstart.md` §2, `spec.md` Architecture Alignment |
| Artisan command (`platform:parity-check`) | `app/Console/Commands/RunParityCheck.php` | Compare native/container parity, persist `parity_results` records, raise drift alerts | Phase 3 T028–T032, `data-model.md` §parity_results |
| Artisan command (`platform:validate-profiles`) | `app/Console/Commands/ValidateEnvironmentProfiles.php` | Run weekly profile validations across both profiles, archive reports | Phase 3 T043–T045, `quickstart.md` §8 |
| Artisan command (`policy:checksum-monitor`) | `app/Console/Commands/PolicyChecksumMonitor.php` | Verify policy acknowledgement headers and report drift | Phase 4 T049–T062, `Operational Cadence & Monitoring` |
| Artisan command (`platform:dependency-review`) | `app/Console/Commands/DependencyReviewReport.php` | Produce monthly dependency catalog reports and open tracking issues | Phase 5 T065–T072 |
| Artisan command (`platform:dependency-review-performance-report`) | `app/Console/Commands/DependencyReviewPerformanceReport.php` | Record monthly dependency review performance observations and publish outputs for QA evidence | Phase 5 T075A |
| Shell scripts (`scripts/profile/use-*.sh`) | `scripts/profile/` directory | Configure environment variables and `.env` files for native/container profiles | Phase 1 T002, Phase 3 T030–T031, `quickstart.md` §1 |
| Shell script (`scripts/platform/bootstrap.sh`) | `scripts/platform/` directory | Developer-friendly wrapper around `platform:bootstrap`, handles secret prompts | Phase 3 T032–T034, `quickstart.md` §2 |
| GitHub workflows (`tests.yml`, `lint.yml`, `browser-tests.yml`, `nightly-heavy.yml`) | `.github/workflows/` | Enforce Bun toolchain, schedule heavy suites, call checksum and validation commands | Phase 4 T046–T064, `Operational Cadence & Monitoring` |
| Workflow suite channels | `app/Models/WorkflowSuiteChannel.php`, `app/Services/BasePlatform/WorkflowSuiteChannelSync.php` | Persist and synchronize multi-destination alert routing for each workflow suite | Phase 4 T055A–T057A, T060A–T062B, `data-model.md` §2.6 |
| Workflow tier policy helper | `app/Support/TieredWorkflowPolicy.php` | Provide tier metadata for CI workflows and SLA mapping | Phase 4 T048, `config/base-platform.php` |
| Observability assets (Prometheus + Grafana) | `config/prometheus/base-platform.yml`, `docs/base-platform/observability/grafana/` | Provide default scrape targets and dashboards for SLAs, parity drift, and CI health | Phase 3 T018, T040, T040A–T040C, `docs/base-platform/observability.md` |

### Environment Validation Alignment

- **Source of Truth**: `environment_profiles` table (Phase 2 T008, Phase 3 T043) and corresponding documentation (`docs/base-platform/environment-validation.md`, `docs/base-platform/environment-support-matrix.md`), including the WSL container profile.
- **Execution**: `platform:validate-profiles` runs weekly (scheduled via Phase 3 T044/T064) across native (macOS/Linux) and container (Linux, macOS, Windows WSL) flows, archives results in `storage/app/base-platform/validation/`, and monthly stewardship tasks verify the environment support matrix while persisting parity logs in `storage/app/base-platform/environment-support.log`.
- **Failure Criteria**: Any parity drift, missing service, or unsupported runtime halts bootstrap workflows (`platform:bootstrap` exits non-zero) and raises a CI failure (`tests.yml` job).
- **Surfacing**: Quickstart (§8) instructs QA to store reports, the environment support matrix document is published in `docs/base-platform/environment-support-matrix.md`, validation artifacts live in `storage/app/base-platform/environment-support.log`, and GitHub Actions retention ensures CI visibility. Tasks checkpoints require QA confirmation before advancing phases.

### Credential & Secret Management Alignment

- **Storage Mechanisms**: GitHub Actions secrets for CI, encrypted `.env` for local (reinforced in `spec.md` FR-007, `quickstart.md` §6, Tasks Phase 3 T041–T042).
- **Rotation & Onboarding**: Documented via `docs/base-platform/credential-rotation.md` and `docs/base-platform/credential-onboarding.md` with actionable steps tied to Phase 3 tasks and Phase 6 T080.
- **Validation**: Phase 1 prerequisites include confirming GitHub secrets (`quickstart.md` §1), while bootstrap scripts (T034) fail gracefully when credentials are missing and direct contributors to the docs.

### Recovery & Fallback Strategy

- Bootstrap recovery helper (T033) and documentation (T035) define retry cadence, escalation owners, and log capture steps.
- Offline/proxy fallback guidance (T036) instructs developers how to mirror registries; parity checks feed into the same guidance when drift stems from network restrictions.
- CI heavy suite failures link to recovery scripts in `scripts/automation/` (T059–T072) and create actionable logs for QA to validate (Phase 4 checkpoint).

### External Dependencies & Mitigations

- **GitHub Actions**: Verified in Phase 1 prerequisites; mitigated via local `policy:checksum-monitor --once` command when CI unavailable.
- **Bun & Playwright**: Version pinning recorded in `docs/base-platform/toolchain-baseline.md` (Phase 1 T004) and `composer.json`/`package.json` tasks (Phase 4 T054–T055).
- **Container Runtime (Podman preferred)**: Quickstart §1 documents Podman Desktop/`podman machine` setup for container parity, with Docker as a fallback only when Podman is unsupported; tasks enforce runtime validation before advancing phases.
- **Observability Stack (Prometheus + Grafana)**: Prometheus scrapes metrics emitted by bootstrap, parity, and validation commands; Grafana dashboards visualize SLAs (SC-001–SC-005). Installation instructions live in `docs/base-platform/quickstart.md` §0, `docs/base-platform/observability.md`, and `toolchain-baseline.md`; tasks T018, T040, T040A–T040C, T077A ensure exporters, scrape configs, and dashboards remain in sync.
- **Credential Providers (Flux)**: Recovery docs include fallback contact and verification steps, satisfying Assumptions mitigation requirements.

### QA Deliverables & Evidence

- Each phase checkpoint lists required artifacts: validation reports, checksum outputs, credential checklist confirmations, parity logs, monthly dependency performance outputs, and the published environment support matrix with its validation log.
- Observability outputs (Prometheus `base-platform.yml`, exporter endpoints, Grafana dashboards) are versioned in-repo and must be validated during Phase 3 checkpoints so QA can confirm SLAs directly from Grafana.
- Quickstart §8–§9 defines where QA stores artifacts (`storage/app/base-platform/validation/`, GitHub Actions run attachments) and the cadence for reviewing nightly jobs prior to releases.
- Phase 6 T081 explicitly confirms presence of policy headers, checksum outputs, profile validation reports, and QA evidence before sign-off.

## Operational Cadence & Monitoring

| Automation | Cadence | Responsible Artifact |
|------------|---------|-----------------------|
| Policy checksum monitor | Nightly + pre-release | `.github/workflows/tests.yml`, `bootstrap/app.php` |
| Environment profile validation | Weekly + pre-release | `.github/workflows/tests.yml`, `bootstrap/app.php`, `docs/base-platform/environment-support-matrix.md` (native + container + WSL) |
| Mutation/browser heavy suites | Nightly + release gate | `.github/workflows/nightly-heavy.yml` |
| Dependency review report + performance log | Monthly | `bootstrap/app.php`, `scripts/automation/dependency-review.sh`, `app/Console/Commands/DependencyReviewPerformanceReport.php` |

All cadence adjustments must be reflected in `plan.md`, `tasks.md` checkpoints, and the quickstart QA section.

## QA Gates & Handoffs

- **Phase entry readiness**: Each phase in `tasks.md` includes a checkpoint ensuring documentation, scripts, and migrations are prepared before implementation begins.
- **User story exit criteria**: Completion requires passing automated tests (unit/feature/architecture), generated documentation updates, and archived validation reports.
- **QA tooling access**: Quickstart now documents commands (`platform:bootstrap`, `platform:validate-profiles`, `policy:checksum-monitor`) and expected logs so QA can execute or verify automation independently.
- **Success criteria mapping**: SC-001–SC-005 map to bootstrap validation, CI performance metrics, dependency review outputs, checklist confirmations, and support-metric tracking, ensuring QA can attest to each outcome.

Support-request reduction (SC-005) will be measured via tagged helpdesk tickets; the process for capturing and reporting this metric is documented in `docs/base-platform/support-metrics.md` and referenced in the quickstart QA workflow.

### Phase Handoff Checkpoints

1. **Setup → Foundational**: QA confirms prerequisite docs (`docs/base-platform/README.md`, `scripts/*/README.md`, `docs/base-platform/toolchain-baseline.md`) list runtime versions, secret checks, and parity prerequisites. Missing content blocks migration work.
2. **Foundational → User Stories**: Database migrations, models, configuration stubs, and metrics helpers load without runtime errors; `BasePlatformServiceProvider` registration verified via `php artisan` bootstrap.
3. **US1 → US2**: Commands `platform:bootstrap`, `platform:parity-check`, and `platform:validate-profiles` pass their new test suites, recovery/offline docs are published, and QA archives validation reports.
4. **US2 → US3**: CI workflows demonstrate Bun parity, nightly heavy suites, and checksum monitor schedules; QA stores GitHub Actions evidence for nightly, weekly, and release-gate runs.
5. **US3 → Polish**: Dependency catalogue JSON, policy docs, support metrics plan, and monthly report sample exist; QA validates `platform:dependency-review` output before final polish tasks begin.
