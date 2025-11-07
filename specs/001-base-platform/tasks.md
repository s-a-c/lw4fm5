# Tasks: Base Platform Foundation

**Input**: Design documents from `/specs/001-base-platform/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are MANDATORY. Define test tasks first, ensure they fail, then implement code to satisfy them per the constitution's TDD mandate.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Prepare documentation scaffolding and profile directories required by all subsequent work.

- [ ] T001 Create environment overview stub in `docs/base-platform/README.md`
- [ ] T002 [P] Add profile switching directory with placeholder doc in `scripts/profile/README.md`
- [ ] T003 [P] Add automation scripts directory note in `scripts/platform/README.md`
- [ ] T004 Record baseline toolchain versions in `docs/base-platform/toolchain-baseline.md`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T005 Create base configuration stub in `config/base-platform.php`
- [ ] T006 [P] Add service provider shell in `app/Providers/BasePlatformServiceProvider.php`
- [ ] T007 [P] Register provider within `bootstrap/providers.php`
- [ ] T008 [P] Scaffold environment profile migration `database/migrations/2025_11_07_000000_create_environment_profiles_table.php`
- [ ] T009 [P] Scaffold toolchain definitions migration `database/migrations/2025_11_07_000100_create_toolchain_definitions_table.php`
- [ ] T010 [P] Scaffold credential policies migration `database/migrations/2025_11_07_000200_create_credential_policies_table.php`
- [ ] T011 [P] Scaffold workflow suites migration `database/migrations/2025_11_07_000300_create_workflow_suites_table.php`
- [ ] T012 [P] Scaffold parity results migration `database/migrations/2025_11_07_000400_create_parity_results_table.php`
- [ ] T013 [P] Create environment profile model in `app/Models/EnvironmentProfile.php`
- [ ] T014 [P] Create toolchain definition model in `app/Models/ToolchainDefinition.php`
- [ ] T015 [P] Create credential policy model in `app/Models/CredentialPolicy.php`
- [ ] T016 [P] Create workflow suite model in `app/Models/WorkflowSuite.php`
- [ ] T017 [P] Create parity result model in `app/Models/ParityResult.php`
- [ ] T018 Establish base metrics helper in `app/Support/BasePlatformMetrics.php`
- [ ] T019 Document parity routines in `docs/base-platform/parity-overview.md`

**Checkpoint**: Foundation ready—QA confirms setup docs, migrations, and tooling prerequisites are satisfied before user story work begins.

---

## Phase 3: User Story 1 - Engineer boots the baseline stack (Priority: P1) 🎯 MVP

**Goal**: Deliver an automated bootstrap workflow supporting both native and container profiles with smoke tests, recovery routines, and metrics.

**Independent Test**: Run `php artisan platform:bootstrap --profile=native` (or `container`) on a clean machine; confirm bootstrap completes, parity check reports pass, recovery guidance surfaces for failures, and metrics/log entries are emitted.

### Tests for User Story 1 (MANDATORY) ⚠️

- [ ] T020 [P] [US1] Add bootstrap workflow feature test in `tests/Feature/BasePlatform/BootstrapWorkflowTest.php`
- [ ] T021 [P] [US1] Add parity check feature test in `tests/Feature/BasePlatform/ParityCheckTest.php`
- [ ] T022 [P] [US1] Add metrics emission unit test in `tests/Unit/BasePlatform/BasePlatformMetricsTest.php`
- [ ] T023 [P] [US1] Add bootstrap recovery unit test in `tests/Unit/BasePlatform/BootstrapRecoveryTest.php`
- [ ] T024 [P] [US1] Add credential policy unit test in `tests/Unit/BasePlatform/CredentialPolicyTest.php`
- [ ] T025 [P] [US1] Add native profile validation feature test in `tests/Feature/BasePlatform/BootstrapNativeProfileTest.php`
- [ ] T026 [P] [US1] Add container profile validation feature test in `tests/Feature/BasePlatform/BootstrapContainerProfileTest.php`

### Implementation for User Story 1

- [ ] T027 [P] [US1] Implement bootstrap command in `app/Console/Commands/RunPlatformBootstrap.php`
- [ ] T028 [P] [US1] Implement parity check command in `app/Console/Commands/RunParityCheck.php`
- [ ] T029 [US1] Implement bootstrap orchestrator service in `app/Services/BasePlatform/BootstrapRunner.php`
- [ ] T030 [P] [US1] Create native profile switch script in `scripts/profile/use-native.sh`
- [ ] T031 [P] [US1] Create container profile switch script in `scripts/profile/use-container.sh`
- [ ] T032 [P] [US1] Add bootstrap shell script wrapper in `scripts/platform/bootstrap.sh`
- [ ] T033 [US1] Implement bootstrap recovery helper in `app/Services/BasePlatform/BootstrapRecovery.php`
- [ ] T034 [US1] Enhance bootstrap shell script to detect missing secrets and emit actionable guidance
- [ ] T035 [US1] Document bootstrap recovery playbook in `docs/base-platform/bootstrap-recovery.md`
- [ ] T036 [US1] Document offline/proxy bootstrap guidance in `docs/base-platform/offline-proxy.md`
- [ ] T037 [US1] Seed baseline configuration via `database/seeders/BasePlatformSeeder.php`
- [ ] T038 [US1] Register bootstrap, parity, recovery, and validation commands/schedules in `bootstrap/app.php`
- [ ] T039 [US1] Extend quickstart instructions in `docs/base-platform/quickstart.md`
- [ ] T040 [US1] Wire bootstrap health checks into `app/Support/BasePlatformMetrics.php`
- [ ] T041 [US1] Document credential rotation playbook in `docs/base-platform/credential-rotation.md`
- [ ] T042 [US1] Document credential onboarding checklist in `docs/base-platform/credential-onboarding.md`
- [ ] T043 [US1] Implement environment validation command in `app/Console/Commands/ValidateEnvironmentProfiles.php`
- [ ] T044 [US1] Schedule environment validation command weekly in `bootstrap/app.php`
- [ ] T045 [US1] Document environment validation workflow in `docs/base-platform/environment-validation.md`

**Checkpoint**: User Story 1 functional—QA archives native/container validation reports, recovery documentation, and credential checklists.

---

## Phase 4: User Story 2 - CI guardians enforce consistency (Priority: P2)

**Goal**: Align GitHub workflows with the standardized toolchain, tiered quality gates, nightly heavy-suite runs, and automated policy acknowledgement monitoring.

**Independent Test**: Trigger CI workflows for a sample branch to confirm Bun-powered jobs complete within SLA, nightly heavy suite schedule enqueues mutation/browser suites, and policy checksum monitoring runs nightly and during release gating.

### Tests for User Story 2 (MANDATORY) ⚠️

- [ ] T046 [P] [US2] Add architecture test ensuring workflows use Bun in `tests/Architecture/GitHubWorkflowComplianceTest.php`
- [ ] T047 [P] [US2] Add architecture test verifying nightly workflow presence in `tests/Architecture/NightlyWorkflowTest.php`
- [ ] T048 [P] [US2] Add unit test confirming tiered policy metadata in `tests/Unit/BasePlatform/TieredWorkflowPolicyTest.php`
- [ ] T049 [P] [US2] Add feature test for policy checksum monitor command in `tests/Feature/BasePlatform/PolicyChecksumMonitorTest.php`

### Implementation for User Story 2

- [ ] T050 [US2] Refactor CI workflow configuration in `.github/workflows/tests.yml`
- [ ] T051 [US2] Refactor lint workflow to Bun in `.github/workflows/lint.yml`
- [ ] T052 [US2] Refactor browser workflow to Bun in `.github/workflows/browser-tests.yml`
- [ ] T053 [US2] Add nightly heavy-suite workflow in `.github/workflows/nightly-heavy.yml`
- [ ] T054 [US2] Update tiered workflow scripts in `composer.json`
- [ ] T055 [US2] Remove npm fallbacks and enforce Bun in `package.json`
- [ ] T056 [US2] Document CI policy and SLAs in `docs/base-platform/ci-policy.md`
- [ ] T057 [US2] Persist workflow suite records via `database/seeders/BasePlatformSeeder.php`
- [ ] T058 [US2] Schedule nightly heavy run in `bootstrap/app.php`
- [ ] T059 [US2] Implement policy checksum monitor script in `scripts/automation/policy-checksum.sh`
- [ ] T060 [US2] Implement policy checksum monitor command in `app/Console/Commands/PolicyChecksumMonitor.php`
- [ ] T061 [US2] Schedule checksum monitor (nightly + release hooks) in `bootstrap/app.php`
- [ ] T062 [US2] Add CI step executing policy checksum monitor in `.github/workflows/tests.yml`
- [ ] T063 [US2] Add CI job executing environment validation command for native and container profiles in `.github/workflows/tests.yml`
- [ ] T064 [US2] Schedule weekly checksum monitor and validation command bundle in `bootstrap/app.php`

**Checkpoint**: User Story 2 functional—QA validates CI workflow updates, scheduled jobs, and checksum monitor outputs.

---

## Phase 5: User Story 3 - Dependency stewardship stays sane (Priority: P3)

**Goal**: Create a governed dependency catalogue, automated review workflow, contribution guidelines, and support-metric tracking plan.

**Independent Test**: Execute dependency review command, confirm catalog output, tracking issue generation, and documentation updates for governance and metrics.

### Tests for User Story 3 (MANDATORY) ⚠️

- [ ] T065 [P] [US3] Add unit test for dependency catalogue parser in `tests/Unit/BasePlatform/DependencyCatalogueTest.php`
- [ ] T066 [P] [US3] Add feature test for dependency review command in `tests/Feature/BasePlatform/DependencyReviewCommandTest.php`
- [ ] T067 [P] [US3] Add unit test for contribution guidelines checklist in `tests/Unit/BasePlatform/ContributionGuidelinesTest.php`

### Implementation for User Story 3

- [ ] T068 [US3] Create dependency catalogue data in `storage/app/base-platform/dependencies.json`
- [ ] T069 [US3] Create dependency policy doc in `docs/base-platform/dependency-policy.md`
- [ ] T070 [US3] Implement dependency review command in `app/Console/Commands/DependencyReviewReport.php`
- [ ] T071 [US3] Add monthly scheduler binding in `bootstrap/app.php`
- [ ] T072 [US3] Add review automation script in `scripts/automation/dependency-review.sh`
- [ ] T073 [US3] Add GitHub issue template in `.github/ISSUE_TEMPLATE/dependency-review.md`
- [ ] T074 [US3] Publish contribution guidelines in `docs/base-platform/contribution-guidelines.md`
- [ ] T075 [US3] Extend seeder with dependency metadata in `database/seeders/BasePlatformSeeder.php`
- [ ] T076 [US3] Document support metric tracking plan in `docs/base-platform/support-metrics.md`

**Checkpoint**: All user stories functional—QA confirms dependency review automation, support metric tracking, and documentation outputs.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Hardening, documentation, and validation tasks affecting multiple stories.

- [ ] T077 [P] Refresh changelog with baseline entry in `docs/base-platform/CHANGELOG.md`
- [ ] T078 Validate combined quickstart flow end-to-end per `docs/base-platform/quickstart.md`
- [ ] T079 [P] Run parity audit report and archive results in `storage/app/base-platform/parity-report.log`
- [ ] T080 [P] Review security posture and credential rotation notes in `docs/base-platform/security-review.md`
- [ ] T081 Final verification: ensure policy acknowledgement headers, checksum monitor outputs, profile validation reports, and QA evidence are present across artifacts in `specs/001-base-platform/`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies—start immediately.
- **Foundational (Phase 2)**: Depends on Setup completion—BLOCKS all user stories.
- **User Stories (Phase 3-5)**: All depend on Foundational completion. Implement in priority order (P1 → P2 → P3) or in parallel once foundation is ready.
- **Polish (Phase 6)**: Depends on completion of intended user stories.

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Phase 2; no dependency on other stories.
- **User Story 2 (P2)**: Requires Phase 2 and US1 bootstrap infrastructure (metrics/recovery hooks).
- **User Story 3 (P3)**: Requires Phase 2 and shared seeder/config updates from US1.

### Within Each User Story

- Write and fail tests (tasks marked [P] in test sections) before implementation tasks.
- Models/config/scripts → Services/commands → Documentation → Scheduling/metrics updates.
- Use checkpoints to validate each story independently before moving on.

### Parallel Opportunities

- Tasks flagged [P] in Phases 1-6 can run concurrently.
- Once foundation complete, user stories can proceed in parallel if team capacity allows.
- Within a story, multiple test tasks and script scaffolds (marked [P]) can run in parallel.

---

## Parallel Example: User Story 1

```bash
# Run tests in parallel (after scaffolding files):
- [ ] T020 [P] [US1] tests/Feature/BasePlatform/BootstrapWorkflowTest.php
- [ ] T021 [P] [US1] tests/Feature/BasePlatform/ParityCheckTest.php
- [ ] T022 [P] [US1] tests/Unit/BasePlatform/BasePlatformMetricsTest.php
- [ ] T023 [P] [US1] tests/Unit/BasePlatform/BootstrapRecoveryTest.php
- [ ] T024 [P] [US1] tests/Unit/BasePlatform/CredentialPolicyTest.php
- [ ] T025 [P] [US1] tests/Feature/BasePlatform/BootstrapNativeProfileTest.php
- [ ] T026 [P] [US1] tests/Feature/BasePlatform/BootstrapContainerProfileTest.php

# Parallel implementation tasks (post-tests):
- [ ] T028 [P] [US1] scripts/profile/use-native.sh
- [ ] T029 [P] [US1] scripts/profile/use-container.sh
- [ ] T030 [P] [US1] scripts/platform/bootstrap.sh
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup.
2. Complete Phase 2: Foundational (BLOCKER).
3. Execute Phase 3: User Story 1 end-to-end, including recovery/credential docs.
4. STOP and validate bootstrap flow using checkpoints and quickstart.

### Incremental Delivery

1. Deliver User Story 1 (MVP) → share bootstrap capability.
2. Deliver User Story 2 → CI standardization & policy monitoring.
3. Deliver User Story 3 → dependency governance & support metrics.
4. Finish with Phase 6 polish tasks.

### Parallel Team Strategy

- After Phase 2, assign US1/US2/US3 to different owners.
- Coordinate on shared files (`bootstrap/app.php`, `database/seeders/BasePlatformSeeder.php`, docs).
- Use checkpoints to merge completed stories safely.

---

## Notes

- [P] tasks target different files to avoid merge conflicts.
- Maintain TDD: ensure tests fail before implementation.
- Keep documentation in sync with automation (CI, quickstart, recovery, policies).
- Update seeder and schedules once per story to avoid conflicts.
- Confirm constitution requirements (policy headers, TDD, observability) remain satisfied throughout delivery.
