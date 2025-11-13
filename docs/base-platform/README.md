# Base Platform Environment Overview

Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Purpose

- Summarize the dual-path developer experience (native Herd profile and container profile)
- Point engineers to bootstrap, parity, and recovery workflows required before feature work
- Track ownership of platform automation assets referenced throughout the quickstart

## Key Components

| Area | Native Profile | Container Profile | Notes |
|------|----------------|-------------------|-------|
| Runtime | PHP 8.5 via Herd | PHP 8.5 via Podman/Docker image | Both require Bun ≥1.1 (Podman v5.7.0+ preferred) |
| Bootstrap | `composer setup -- --profile=native` | `composer setup -- --profile=container` | Wraps `platform:bootstrap` command |
| Parity Validation | `php artisan platform:validate-profiles --profile=native` | `php artisan platform:validate-profiles --profile=container` | Weekly schedule handles `--all` |
| Secrets | Encrypted `.env.native` | Encrypted `.env.container` | Rotation documented in credential guides |

## Required Artifacts

- `docs/base-platform/toolchain-baseline.md` records version pins used by automation
- `docs/base-platform/observability.md` documents Prometheus scrape targets and Grafana dashboards required for local monitoring
- `scripts/profile/` contains profile switch helpers invoked by bootstrap tooling
- `scripts/platform/` includes shell wrappers for console commands
- `storage/app/base-platform/validation/` archives validation outputs for QA

## Contacts

- **Platform Engineering**: Maintains artisan commands, shell scripts, and scheduled jobs
- **DevEx**: Oversees GitHub Actions workflow health and Bun parity across pipelines

## Next Steps

- Follow `docs/base-platform/quickstart.md` to select a profile
- Run `platform:bootstrap` and parity checks as outlined in Phase 3 tasks
- Consult recovery playbooks before escalating bootstrap or parity failures
