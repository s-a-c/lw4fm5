# Contribution Guidelines

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Baseline Principles

- Meet the service-level agreements defined in the specification: bootstrap ≤45 minutes (SC-001), CI P90 ≤25 minutes (SC-002), monthly dependency governance (SC-003), and support ticket reduction (SC-005).
- Keep the dependency catalogue (`storage/app/base-platform/dependencies.json`) authoritative—update owner, cadence, and notes whenever a dependency changes.
- Use published automation scripts instead of bespoke commands; this guarantees parity between local runs and CI (`composer workflow:*`, `php artisan platform:*`, `scripts/automation/*.sh`).

## Required Pull Request Checklist

Complete this list before requesting review. Copy/paste into your PR description or personal notes.

- [ ] composer workflow:core
- [ ] composer analyse
- [ ] php artisan test --group=feature --group=unit
- [ ] php artisan policy:checksum-monitor --once
- [ ] php artisan platform:dependency-review --output="base-platform/dependency-reports/$(date +%Y-%m)-dependency-review.json"
- [ ] php artisan platform:dependency-review-performance-report --report="base-platform/dependency-reports/$(date +%Y-%m)-dependency-review.json"
- [ ] ./scripts/automation/dependency-review.sh (for monthly governance windows)
- [ ] Update documentation and evidence links referenced below

> **Tip:** When working on dependency updates, run the checklist for both native and container profiles. Capture failures in the governance issue template (`.github/ISSUE_TEMPLATE/dependency-review.md`).

## QA Evidence & Links

- Store dependency review artifacts under `storage/app/base-platform/dependency-reports/`.
- Append runtime/performance entries to `storage/app/base-platform/dependency-performance.log`.
- Record environment validation parity in `storage/app/base-platform/environment-support.log`.
- Update support ticket metrics per [Support Metrics & Evidence Plan](./support-metrics.md) and link notable deltas.
- Reference the monthly tracking issue generated from `.github/ISSUE_TEMPLATE/dependency-review.md`.

## Monthly Dependency Stewardship

1. Run `./scripts/automation/dependency-review.sh` (or equivalent CI workflow) on the first business day of the month.
2. Attach the generated report and performance log to the governance issue.
3. Update `dependencies.json` with new review timestamps, cadences, or notes.
4. Document outcomes and remediation tasks in the dependency review issue template.

## Support & Escalation

- Platform Engineering owns automation and catalogue updates.
- DevEx monitors CI stability and collaborates on remediation when severity counts fail thresholds.
- Surface support ticket trends in the monthly report (see [Support Metrics & Evidence Plan](./support-metrics.md)).
