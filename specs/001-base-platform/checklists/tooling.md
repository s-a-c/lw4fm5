# Tooling Installation & Configuration Requirements Checklist

---

<details>
<summary>Expand for Table of Contents</summary>

- [Tooling Installation \& Configuration Requirements Checklist](#tooling-installation--configuration-requirements-checklist)
  - [1. Requirement Completeness](#1-requirement-completeness)
  - [2. Requirement Clarity](#2-requirement-clarity)
  - [3. Requirement Consistency](#3-requirement-consistency)
  - [4. Acceptance Criteria Quality](#4-acceptance-criteria-quality)
  - [5. Scenario Coverage](#5-scenario-coverage)
  - [6. Edge Case Coverage](#6-edge-case-coverage)
  - [7. Non-Functional Requirements](#7-non-functional-requirements)
  - [8. Dependencies \& Assumptions](#8-dependencies--assumptions)
  - [9. Ambiguities \& Conflicts](#9-ambiguities--conflicts)

</details>

---

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

**Purpose**: Validate that requirements for installing, configuring, and maintaining composer (PHP) and Bun/Node packages—as defined in `composer.json` and `package.json`—are complete, clear, consistent, measurable, and aligned with baseline automation.

**Created**: 2025-11-08
**Depth**: Formal release gate rigor
**Audience**: Platform engineers (maintainers), onboarding contributors, QA/release reviewers
**Scope**: Entire package manifest coverage (runtime and dev) including private feeds, scripts, and automation hooks.

## 1. Requirement Completeness

- [x] CHK201 Are installation instructions documented for every composer repository requiring authentication (`composer.json` repositories section)? [Completeness, Spec §FR-007, Quickstart/Docs]
- [x] CHK202 Do docs list all mandatory composer packages (runtime + dev) with justification or references to where their configuration lives? [Completeness, Spec §FR-003/FR-004]
- [x] CHK203 Are Bun/Node prerequisites (Node/Bun versions, package manager commands) documented for each dependency in `package.json`? [Completeness, Spec §FR-014, Quickstart]
- [x] CHK204 Is there guidance for installing optional/experimental packages and the conditions under which they should be enabled/disabled? [Completeness, Spec §FR-003, Dependency Policy]
- [x] CHK205 Are post-install steps (migrations, asset builds, provider registration) tied to specific packages documented in setup guides or scripts? [Completeness, Spec §FR-001/FR-004]

## 2. Requirement Clarity

- [x] CHK206 Are repository URLs, credentials, and token scopes clearly stated for private composer feeds (`composer.fluxui.dev`, `satis.spatie.be`)? [Clarity, Spec §FR-007, Credential Onboarding]
- [x] CHK207 Do docs specify exact commands (`composer install`, `bun install`, script aliases) and flags required for a clean setup? [Clarity, Spec §FR-001, Quickstart]
- [x] CHK208 Are environment variables or `.env` entries added by package scripts documented (e.g., Horizon, Telescope, Typesense)? [Clarity, Spec §FR-007, Config docs]
- [x] CHK209 Do requirements explain how Bun-integrated scripts replace npm/yarn equivalents, including fallback scenarios? [Clarity, Spec §FR-014, Tasks §US2]
- [x] CHK210 Are version pins and upgrade cadence for critical packages (Laravel, Bun, Pest, Playwright) explicitly defined? [Clarity, Spec §FR-003/FR-009, Toolchain Baseline, Dependency Policy]

## 3. Requirement Consistency

- [x] CHK211 Do runtime versionRequirements in docs match composer constraints (`php ^8.5`, package versions) and Bun engine requirements? [Consistency, Spec §FR-014, Toolchain Baseline]
- [x] CHK212 Are script names and usage consistent between documentation, `composer.json` scripts, and `package.json` scripts? [Consistency, Spec §FR-004, Plan §Automation]
- [x] CHK213 Do quickstart, recovery, and CI policy docs refer to the same commands for lint/test/build workflows without conflicting instructions? [Consistency, Spec §FR-005/FR-015]
- [x] CHK214 Are package classification labels (core/optional/experimental) consistent between dependency policy and catalogue JSON? [Consistency, Spec §FR-003, Dependency Policy]
- [x] CHK215 Do Windows WSL instructions align with the same package versions and commands as native Linux/macOS docs? [Consistency, Spec Assumptions, Quickstart, Environment Matrix]

## 4. Acceptance Criteria Quality

- [x] CHK216 Are success criteria for package installation (e.g., `composer install` exits 0, `bun run build` produces assets) defined with observable checks? [Acceptance Criteria, Spec §FR-001/FR-004]
- [x] CHK217 Do docs specify evidence locations (logs, artifacts) that QA must review to confirm package installation succeeded? [Acceptance Criteria, Plan §QA Deliverables]
- [x] CHK218 Is there a measurable acceptance criterion for dependency review tasks (counts, report artifacts) in docs? [Acceptance Criteria, Spec §FR-009, Dependency Policy]
- [x] CHK219 Are rollback/cleanup steps documented when a package upgrade fails tests? [Acceptance Criteria, Spec §Edge Cases, Recovery Docs]
- [x] CHK220 Do configuration docs describe how to verify CI pipelines are using the declared package versions (P90, flake rate metrics)? [Acceptance Criteria, Spec §SC-002, CI Policy]

## 5. Scenario Coverage

- [x] CHK221 Are installation requirements documented for both clean environments and upgrade scenarios (existing installs)? [Coverage, Spec §FR-001/FR-009]
- [x] CHK222 Do docs cover containerized setups (Sail with Podman preferred, Docker fallback) as well as native Herd/Bun installations for package commands? [Coverage, Spec §FR-013, Quickstart]
- [x] CHK223 Are recovery instructions provided for composer or Bun install failures (network, mirrors, credential) per edge cases? [Coverage, Spec Edge Cases, Recovery docs]
- [x] CHK224 Are continuous integration scenarios documented (cache warm-up, re-install on CI runners, Playwright browser install)? [Coverage, Spec §FR-005/FR-015, CI Policy]
- [x] CHK225 Do docs describe how to handle experimental packages during the dependency review cadence (promote/demote workflow)? [Coverage, Spec §FR-003, Dependency Policy]
- [x] CHK225A Are Prometheus scrape configs and Grafana dashboards included with documented installation steps for local observability? [Coverage, Spec §FR-006, Observability doc, config/prometheus/base-platform.yml]

## 6. Edge Case Coverage

- [x] CHK226 Is there guidance for offline or proxied environments when fetching composer/Bun packages (mirrors, artifact repos)? [Edge Case, Spec Edge Cases, Offline/Proxy doc]
- [x] CHK227 Are remediation steps documented when package scripts fail due to missing system dependencies (e.g., Playwright browsers, Tailwind oxide binaries)? [Edge Case, Spec §FR-006/FR-014]
- [x] CHK228 Do docs address conflicts between Bun and Node version managers or global installations? [Edge Case, Spec Assumptions, Toolchain Baseline]
- [x] CHK229 Are procedures described for rebuilding vendor caches when corrupted (composer cache clear, Bun cache)? [Edge Case, Spec §FR-008, Recovery docs]
- [x] CHK230 Do requirements cover handling security advisories for packages (e.g., composer audit output)? [Edge Case, Spec §FR-009, Support Metrics/Dependency Policy]

## 7. Non-Functional Requirements

- [x] CHK231 Are performance considerations for package scripts documented (e.g., `workflow:core` target P90, caching strategies)? [Non-Functional, Spec §SC-002, CI Policy]
- [x] CHK232 Do docs state observability hooks related to package operations (logs, metrics, health checks)? [Non-Functional, Spec §FR-006, Plan §Observability]
- [x] CHK233 Are security requirements (credential storage, token rotation) for private packages captured? [Non-Functional, Spec §FR-007, Credential docs]
- [x] CHK234 Is the dependency audit process (composer audit, security advisories) documented with thresholds or required actions? [Non-Functional, Spec §FR-009, Dependency Policy]
- [x] CHK235 Are upgrade frequency and maintenance windows for critical packages documented (e.g., monthly review cadence)? [Non-Functional, Spec §FR-009, Plan §Operational Cadence]

## 8. Dependencies & Assumptions

- [x] CHK236 Are all external registries, mirrors, and package feeds listed with availability assumptions? [Dependencies & Assumptions, Spec §Assumptions, Quickstart]
- [x] CHK237 Do docs identify which team owns each package group (e.g., spatie suite, Playwright, Bun plugins)? [Dependencies & Assumptions, Plan §Automation Ownership, Dependency Policy]
- [x] CHK238 Are environment prerequisites (PHP extensions, system packages, Podman version with Docker fallback) documented alongside package installation steps? [Dependencies & Assumptions, Spec §FR-001, Quickstart]
- [x] CHK239 Is the workflow for updating lockfiles (`composer.lock`, `bun.lock`) specified, including validation steps? [Dependencies & Assumptions, Spec §FR-004, Tasks]
- [x] CHK240 Are assumptions about minimum Node/Bun versions and package manager availability validated in docs? [Dependencies & Assumptions, Spec §FR-014, Toolchain Baseline]

## 9. Ambiguities & Conflicts

- [x] CHK241 Are there vague terms in docs like “configure package” or “ensure compatibility” that need explicit commands or criteria? [Ambiguity, Spec §FR-004, Docs]
- [x] CHK242 Do any instructions conflict between quickstart, scripts, and CI policy regarding package commands or versions? [Conflict, Spec §FR-005/FR-015]
- [x] CHK243 Is the process for handling composer/bun script failures during CI clearly documented without contradictory steps? [Ambiguity/Conflict, Spec §FR-008, CI Policy]
- [x] CHK244 Are packages marked experimental in `dependencies.json` clearly labeled as such in documentation to avoid confusion? [Ambiguity, Spec §FR-003, Dependency Policy]
- [x] CHK245 Are instructions for reinstalling or removing packages consistent across docs (no conflicting `composer remove` vs manual edits guidance)? [Conflict, Spec §FR-004, Documentation cross-check]
