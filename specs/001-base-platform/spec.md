# Feature Specification: Base Platform Foundation
Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

**Feature Branch**: `001-base-platform`
**Created**: 2025-11-07
**Status**: Draft
**Input**: User description: "this project is not yet fully setup to fdirm the basis of a project - there are gaps in configuration and setup of composer packages, composer scrtipts, github workflows and javascripy (via bun) packages"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Engineer boots the baseline stack (Priority: P1)

Platform engineers need to go from a clean machine to a running application with one guided workflow that installs all backend packages, JavaScript dependencies, environment files, and seeds project scaffolding without manual patchwork.

**Why this priority**: Without a reliable setup path, every feature team bleeds time rediscovering missing steps, which blocks all downstream delivery.

**Independent Test**: Provision a new workstation, run the documented bootstrap command(s), and confirm the end-to-end setup completes without intervention, resulting in a green health check, working queues, and assets compiling through the designated JavaScript runtime.

**Acceptance Scenarios**:

1. **Given** a laptop with only required system prerequisites installed, **When** the engineer follows the baseline quickstart, **Then** backend, queue, and frontend build scripts succeed without manual edits to configuration files.
2. **Given** an engineer missing private package credentials, **When** they run the bootstrap workflow, **Then** the process halts with actionable guidance on retrieving credentials rather than failing mid-stream.

---

### User Story 2 - CI guardians enforce consistency (Priority: P2)

Release managers rely on automation to mirror the local baseline so that pull requests fail fast on dependency drift, linting, tests, and browser automation using the same runtime choices defined for contributors.

**Why this priority**: Stable automation protects velocity and avoids “works on my machine” regressions when package scripts, runtime versions, or browser automation dependencies fall out of sync.

**Independent Test**: Open a pull request that triggers lint, unit, type, mutation, and browser jobs using the updated workflows; each run must pass using the standardized toolchain and cache strategy in under 25 minutes without manual reruns.

**Acceptance Scenarios**:

1. **Given** a branch with passing local checks, **When** GitHub Actions runs the consolidated workflow, **Then** the pipeline uses the same runtime versions defined in the baseline matrix and completes inside the agreed SLA.
2. **Given** the chosen JavaScript runtime or browser automation toolkit releases a breaking change, **When** the dependency cache invalidates, **Then** the workflow rehydrates deterministically because lockfiles and install steps are aligned across jobs.
3. **Given** a contributor targets an unsupported branch, **When** workflows evaluate branch filters, **Then** the pipeline short-circuits with messaging about supported release branches instead of silently running stale jobs.

---

### User Story 3 - Dependency stewardship stays sane (Priority: P3)

Technical leads want a governed process that documents which backend and frontend packages are in-scope, why they are included, and how to upgrade or remove them without breaking the baseline.

**Why this priority**: The current manifest mixes experimental packages (for example dev-master branches, dual private feeds, or npm-only scripts) without corresponding configuration or owners, making future maintenance risky.

**Independent Test**: Review the dependency catalogue, update a flagged package via the documented process, and verify that automation (lint/test/build) passes while changelog entries capture impacts and rollback steps.

**Acceptance Scenarios**:

1. **Given** a dependency is marked “core,” **When** a developer proposes removal, **Then** the catalogue lists owning team, justification, and required checks before approval.
2. **Given** a scheduled monthly health check, **When** the dependency freshness script runs, **Then** it outputs actionable upgrade tasks (backend + frontend) and opens a tracking issue or task automatically.

---

### Edge Cases

- How does the platform respond when GitHub Actions lacks required secrets (registry credentials, browser automation tokens) or the selected JavaScript runtime is unavailable on the runner image, and how are recovery steps communicated to the engineer?
- How is version drift handled when contributors use Windows (via WSL) or Linux environments that cannot rely on macOS-specific tooling currently assumed by local docs?
- How do bootstrap, parity, and recovery workflows operate when developers run offline or behind restrictive proxies that block private package repositories or artifact mirrors, and what mirrored registry guidance must be provided?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Deliver a single documented bootstrap workflow (scripted or make target) that configures the supported backend runtime, authenticates against private package feeds, applies schema migrations, starts queue workers, and builds frontend assets without manual file edits.
- **FR-002**: Publish an environment support matrix (minimum macOS + Linux) in `docs/base-platform/environment-support-matrix.md` covering prerequisites, supported runtime versions, and containerized versus host-managed alternatives, with automated validation for each path (native and container) that fails the build if either flow breaks and archives results in `storage/app/base-platform/environment-support.log`.
- **FR-003**: Establish a dependency catalogue that classifies backend and frontend packages into “core,” “optional,” and “experimental,” including owners, rationale, and deprecation policy.
- **FR-004**: Standardize automation scripts so that setup, lint, quality, and test commands are idempotent, parallel-safe, and runnable both locally and in CI; deprecate redundant or failing scripts and document replacements.
- **FR-005**: Align GitHub workflows (tests, lint, browser) to a shared toolchain configuration (matrix or reusable workflow) that reuses the same runtime definitions, unifies versioning, and caches dependencies consistently.
- **FR-006**: Provide observability hooks (health checks, smoke tests) that confirm queues, schedulers, and asset builds are functioning post-bootstrap and surface actionable errors when they fail.
- **FR-007**: Define credential management rules covering private package feeds, browser automation downloads, and environment secrets, including the agreed solo-developer baseline of GitHub Actions secrets for CI and encrypted local `.env` storage, published rotation playbooks, onboarding checklists, and fallback behaviour for future collaborators.
- **FR-008**: Document and automate recovery procedures for failed automation (bootstrap, parity, CI heavy suites, asset builds) with time-boxed retries and escalation steps.
- **FR-009**: Deliver a monthly dependency review workflow that triggers automated reports (e.g., outdated package listings), records performance observations, and creates tasks for the platform backlog.
  - Performance reports must capture command runtime, success/failure state, counts of outdated packages by severity, and references to any follow-up tasks or issues opened from the run.
- **FR-010**: Ensure policy acknowledgements remain current across all generated artifacts (specs, plans, automation outputs) with an automated checksum monitor that runs nightly and before tagged releases, alerting on drift.
- **FR-011**: Define baseline data and configuration seeding required for downstream feature work, including synthetic accounts, queue configuration, and monitoring dashboards.
- **FR-012**: Provide contribution guidelines that map feature work to baseline expectations (testing levels, scripts to run, required Git hooks) so new product teams can self-serve onboarding.
- **FR-013**: Provide dual-path local development support where both containerized services and host-managed runtimes are documented, validated, and kept in parity through automated smoke tests.
- **FR-014**: Mandate the current Bun-based JavaScript toolchain across local and CI workflows, with documented migration plans for any remaining npm-only scripts.
- **FR-015**: Implement a tiered workflow policy where linting, unit, and type checks are mandatory on every push, while mutation and browser suites execute nightly and are required to pass before any tagged release or hotfix deployment.

### Key Entities *(include if feature involves data)*

- **Environment Profile**: Describes supported OS/tool combinations, bootstrap commands, and validation checks that confirm installs succeeded.
- **Dependency Catalogue**: Living inventory of backend and frontend packages with ownership, classification, and lifecycle metadata.
- **Automation Suite**: Collection of GitHub workflows, local scripts, and health checks that enforce baseline quality gates.
- **Credential Policy**: Guidance for managing required secrets (private registries, automation tokens, database credentials) including rotation cadence and onboarding/offboarding steps.
- **Workflow Suite Channel**: Mapping between workflow suites and the Slack, email, or webhook destinations that receive failure alerts.

### Assumptions

- Existing package manifests are authoritative starting points but require pruning or justification before go-live.
- Feature teams depend on GitHub Actions; no alternative CI service is planned in the immediate roadmap.
- Database, queue, and cache services will remain the standard Laravel stack (PostgreSQL, Redis) unless future specs dictate otherwise.
- The designated JavaScript runtime is Bun and is expected to remain standard across local and CI environments; any future change must preserve the guarantees defined in this specification.
- Containerized and native development paths must remain functionally equivalent, with automated parity checks ensuring neither path lags behind.
- Windows developers are supported through a WSL-driven container profile that mirrors the Linux container flow; native Windows runtimes are out of scope.
- Current scope assumes a solo developer managing local credentials via encrypted `.env` files; future team expansion must revisit access provisioning and auditing needs.
- Base Platform automation lives entirely within the Storefront service; it must not introduce cross-service integrations, and ongoing maintenance ownership resides with the Platform Engineering team.

## Architecture Alignment

### Service Boundaries

- Base Platform automation operates exclusively inside the Storefront repository. No new cross-service APIs, database links, or workflow triggers are introduced. GitHub Actions jobs interact only with Storefront code and existing first-party services (PostgreSQL, Redis, Horizon).
- Explicitly out of scope: communicating with payment, merchandising, or marketing services; publishing events to external queues; depending on other monorepos. Future integrations require a new specification.

### Responsibility Matrix

| Component | Primary Role | Owner | Related Artifacts |
|-----------|--------------|-------|-------------------|
| Laravel commands (`platform:bootstrap`, `platform:parity-check`, `platform:validate-profiles`, `policy:checksum-monitor`, `platform:dependency-review`) | Orchestrate domain logic, emit metrics/logs, and coordinate database state | Platform Engineering | `app/Console/Commands/*.php`, `app/Services/BasePlatform/*`, `app/Support/BasePlatformMetrics.php` |
| Shell scripts (`scripts/platform/*.sh`, `scripts/profile/*.sh`, `scripts/automation/*.sh`) | Provide developer ergonomics and CI shell entrypoints; wrap artisan commands with environment setup | Platform Engineering | Script directories noted in `tasks.md` Phase 1 & 3 |
| GitHub workflows (`tests.yml`, `lint.yml`, `browser-tests.yml`, `nightly-heavy.yml`) | Enforce parity between local and CI executions, schedule heavy suites, surface checksum drift | Platform Engineering (configuration) with DevEx (operations) | `.github/workflows/*.yml`, `plan.md` Operational Cadence table |

### Environment Validation Coverage

- Dual-profile validation is mandated by **FR-002**. Bootstrap flows, parity checks, and weekly validation schedules cover both native and container profiles. Failure criteria are documented in `tasks.md` (Phase 3, T020–T045) and surfaced to QA through `quickstart.md` §8.
- Validation output destinations are fixed (`storage/app/base-platform/validation/` for reports, GitHub Actions artifacts for CI), ensuring results remain auditable.

### Recovery & Fallback Coverage

- Recovery flows cover bootstrap, parity, CI heavy suites, and profile validation. Documentation tasks (T034–T036, T045) guarantee actionable guidance for missing secrets, offline/proxy scenarios, and retry escalation paths.
- Escalation timelines align with **FR-008**: retry once within 15 minutes, then escalate automatically to Platform Engineering through GitHub workflow notifications.

### Observability Hooks

- Each command and script emits structured logs and Prometheus metrics via `app/Support/BasePlatformMetrics.php`, covering bootstrap health, parity drift, checksum compliance, and dependency review status.
- Smoke tests validate queues, schedulers, and asset builds immediately after bootstrap. Weekly validation jobs reuse the same metrics pipeline to keep observability consistent across automation entry points.

### Scheduling Cadence Consistency

- Nightly: heavy suite workflow (`nightly-heavy.yml`) and checksum monitor.
- Weekly: environment validation command bundling both profiles.
- Pre-release: checksum monitor plus environment validation and heavy suite confirmation before tagging.
- Monthly: dependency review (`platform:dependency-review`) with automated issue creation.
These cadences mirror the Operational Cadence table in `plan.md` and the scheduling tasks in `tasks.md` Phase 4–5.

### Success Criteria Traceability

| Success Criterion | Architectural Mechanism | Tasks |
|-------------------|-------------------------|-------|
| **SC-001** 45-minute bootstrap SLA | `platform:bootstrap`, profile switch scripts, smoke tests, recovery guides | Phase 3 T027–T045 |
| **SC-002** CI <25 minutes, <5% flake | Bun-aligned workflows, tiered policy metadata, nightly heavy suites | Phase 4 T046–T064 |
| **SC-003** Monthly dependency review | `platform:dependency-review`, catalog JSON, GitHub issue template | Phase 5 T065–T075 |
| **SC-004** Quality gate adoption | Composer scripts, workflow suite records, quickstart QA checklist | Phases 3–4, 6 T078–T081 |
| **SC-005** Support ticket reduction | Credential onboarding/rotation docs, recovery playbooks, QA workflow | Phase 3 docs T035–T045, Phase 6 T076 |

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of new engineers can execute the documented bootstrap within 45 minutes on supported OSs, achieving passing smoke tests on first attempt.
- **SC-002**: GitHub workflows (lint + test + browser) complete within 25 minutes P90 and exhibit <5% flake rate across rolling seven-day windows.
- **SC-003**: Dependency review pipeline produces a monthly report with zero missing owner assignments, includes performance reporting outputs, and has no unreviewed critical security advisories older than seven days.
- **SC-004**: At least 95% of merged pull requests reference baseline quality gates (lint, type checks, tests) in their checklists, indicating adoption of standardized scripts.
- **SC-005**: Support requests related to local setup drop by 80% within two sprints after the baseline launch, measured via internal helpdesk tags.

## Clarifications

### Session 2025-11-07
- Q: How should registry and automation secrets (e.g., Flux credentials) be managed across local and CI workflows? → A: Use GitHub Actions secrets for CI plus encrypted local `.env` storage for the solo developer.
- Q: When should mutation and browser suites run within the tiered workflow policy? → A: Execute nightly and require completion before tagged releases or hotfixes.
### Session 2025-11-08
- Q: How many notification destinations should each workflow suite support? → A: Store channels in a separate table so suites can own multiple notification targets.
- Q: What level of Windows support is required? → A: Support Windows via WSL container profile; native Windows runtimes are out of scope.
- Q: What metrics must the dependency review performance report include? → A: Runtime, success state, outdated package counts by severity, and links to follow-up tasks/issues.
