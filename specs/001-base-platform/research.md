Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Research: Base Platform Foundation

## Overview

Research focused on resolving platform-level uncertainties: dual-path local development, Bun standardization, secrets management for the current solo developer, scheduling of heavy quality gates, and automation coverage to keep the baseline healthy as the team expands.

## Findings

### Dual Development Paths
- **Decision**: Support both containerized and native (Herd/homebrew) local environments with automated parity checks.
- **Rationale**: Ensures macOS-native velocity while keeping a documented container path for Linux/CI parity and future collaborators; parity checks prevent drift.
- **Alternatives considered**: Container-only (higher resource cost, slower feedback); native-only (blocks onboarding for non-macOS contributors).

### JavaScript Toolchain
- **Decision**: Mandate Bun across local and CI workflows, migrate remaining npm-only scripts, and document fallbacks.
- **Rationale**: Bun already configured; standardization reduces flake risk and aligns with existing composer scripts that call Bun commands.
- **Alternatives considered**: Dual Bun/Node support (higher maintenance, inconsistent tooling); revert to Node/npm (loses performance and existing investment).

### Credential Management
- **Decision**: Store CI secrets in GitHub Actions, keep local secrets in encrypted `.env` files (solo developer baseline), and document rotation plus onboarding steps for future collaborators.
- **Rationale**: Matches current access scope, keeps automation reproducible, and scales to multi-user setups with minimal rework.
- **Alternatives considered**: Centralized vault (overhead for single contributor); ad-hoc manual storage (security risk, audit gaps).

### Heavy Quality Gates
- **Decision**: Run mutation and browser suites nightly and require green status before tagged releases or hotfixes.
- **Rationale**: Preserves fast feedback on regular pushes while guaranteeing deep coverage prior to sensitive releases.
- **Alternatives considered**: Manual-only runs (risk of skipped checks); running on every push (unacceptably slow for daily work).

### Baseline Observability
- **Decision**: Implement bootstrap smoke tests (queues, schedulers, asset build), structured logs with correlation IDs, and metrics tracking bootstrap SLA and CI reliability.
- **Rationale**: Early detection of misconfiguration preserves developer productivity and satisfies constitution observability requirements.
- **Alternatives considered**: Ad-hoc manual verification (high toil); deferring observability (risks silent regressions).

## Implications

- Automation scripts must orchestrate both Docker-based and native setup flows and feed parity smoke tests.
- Documentation needs clear credential handling steps and guidelines for future team expansion.
- CI pipeline updates must incorporate Bun and nightly heavy suite scheduling.
- Monitoring/alerting should surface bootstrap failures and CI flake increases promptly.
