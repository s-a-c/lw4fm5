# CI Policy & Service-Level Agreements

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Overview

- **Core Quality Gate**: Executes on every push/PR (`tests.yml`) and enforces static analysis, unit coverage, and policy acknowledgement integrity.
- **Heavy Quality Gate**: Executes nightly (`nightly-heavy.yml`) and before releases, covering mutation testing and Playwright browser checks.
- **Browser Regression**: Reuses the heavy suite for ad-hoc verification (`browser-tests.yml`).

## Tiers & SLA Targets

| Tier | Workflows | Checks | SLA (minutes) |
|------|-----------|--------|---------------|
| Core | `tests.yml`, `lint.yml` | Lint (Pint/Prettier), Rector dry-run, PHPStan, unit/perf, security audit | ≤ 25 |
| Heavy | `nightly-heavy.yml`, `browser-tests.yml` | Mutation tests, Playwright smoke suite | ≤ 120 |

## Notification Channels

| Workflow Suite | Channels |
|----------------|----------|
| core-quality | `slack::#ci-core-quality` |
| heavy-quality | `slack::#ci-heavy-quality`, `email::platform-alerts@example.com` |

Channeled alerts are managed via `workflow_suite_channels` config and synchronized during deploys.

## Policy Checksum Monitor

- Command: `php artisan policy:checksum-monitor`
- Script: `scripts/automation/policy-checksum.sh`
- Scheduled nightly (`bootstrap/app.php`) and invoked within CI workflows.
- Fails builds if acknowledgement headers drift from checksum `v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e`.

## SLA Results (2025-11-09)

| Metric | Result | Target | Source |
|--------|--------|--------|--------|
| Core workflow P90 (`tests.yml`) | **22.6 minutes** | ≤ 25 minutes | `gh api repos/:owner/:repo/actions/workflows/tests.yml/runs --paginate` sampled 2025-11-02–2025-11-09 |

- Sample command (requires `gh` CLI):\
  `gh api repos/<org>/<repo>/actions/workflows/tests.yml/runs --paginate --jq '.workflow_runs[] | select(.event == \"push\") | .run_duration_ms'`\
  Aggregate with spreadsheet or script to derive P90; archived notes stored in `docs/base-platform/ci-policy.md`.
- Latest review timestamp: `2025-11-09T21:39Z` (see documentation checklist sign-off).

## Environment Validation

- Scheduled weekly (`platform:validate-profiles --all`) and executed per CI job.
- Artifacts stored in `storage/app/base-platform/validation/` and uploaded from CI for QA review.

## Playwright / Browser Policy

- Playwright browsers cached via Bun.
- Browser regression triggered nightly and may be run on-demand by QA (`composer workflow:heavy`).

## Runbook Links

- [Bootstrap Recovery](./bootstrap-recovery.md)
- [Credential Onboarding](./credential-onboarding.md)
- [Environment Validation](./environment-validation.md)
