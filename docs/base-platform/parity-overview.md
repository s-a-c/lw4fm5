# Parity Validation Overview

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Purpose

- Keep native and container development profiles in lockstep
- Detect runtime drift (PHP, Bun, database, queues) before feature work begins
- Provide actionable recovery guidance when profiles diverge

## Workflow

1. Run `php artisan platform:bootstrap --profile=<profile>` to prepare the environment.
2. Execute `php artisan platform:parity-check --profile=<profile>` to capture profile-specific issues.
3. Schedule `php artisan platform:validate-profiles --all` weekly via CI to compare both profiles.
4. Archive generated reports under `storage/app/base-platform/validation/` with timestamped filenames.

## Data Storage

- `environment_profiles` defines the supported profiles, prerequisites, and smoke scripts.
- `parity_results` stores execution history, status, and issue payloads.
- `workflow_suites` references validation jobs required for release gating.
- `toolchain_definitions` tracks runtime versions used during parity comparisons.

## Recovery

- If `status = warning` or `fail`, follow `docs/base-platform/bootstrap-recovery.md`.
- Update toolchain definitions and re-run the parity command after applying fixes.
- Document residual drift and escalation steps in the QA evidence folder per Phase 3 checkpoints.
