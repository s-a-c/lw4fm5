# Support Metrics & Evidence Plan

Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Purpose

- Define the metrics required to achieve Success Criterion SC-005 (support tickets reduced by 80%).
- Describe data sources, collection cadence, and evidence storage for bootstrap, CI, and queue throughput metrics.
- Provide guidance for QA/release reviewers when validating documentation checkpoints in tasks Phase 6 (T076, T079A).

## Metrics Overview

| Metric | Target | Data Source | Collection Cadence | Evidence Storage |
|--------|--------|-------------|--------------------|------------------|
| Bootstrap completion time | ≤ 45 minutes (SC-001) | Local engineer reports, `storage/app/base-platform/bootstrap-timings.json` | Monthly + after major changes | `storage/app/base-platform/bootstrap-timings.json` |
| Support ticket reduction | ≥ 80% drop two sprints post-launch (SC-005) | Helpdesk system (tag: `baseline-support`) | Sprint review | `storage/app/base-platform/support-metrics.log` |
| CI P90 duration | ≤ 25 minutes (SC-002) | GitHub Actions API (`tests.yml`) | Weekly | `docs/base-platform/ci-policy.md` (results section) |
| Queue throughput | ≥ 1k jobs/min (Plan requirement) | Horizon metrics or log exports | Monthly | `storage/app/base-platform/queue-throughput.log` |
| Dependency review runtime | Runtime + status + severity counts (FR-009) | `php artisan platform:dependency-review`, `php artisan platform:dependency-review-performance-report` | Monthly | `storage/app/base-platform/dependency-performance.log` |
| Environment parity validation | Profiles remain supported (FR-002) | `php artisan platform:validate-profiles --all` | Weekly + monthly | `storage/app/base-platform/environment-support.log` |
| Quality gate adoption | ≥ 95% of merged PRs complete checklist (SC-004) | PR checklist audit + weekly QA report | Weekly | `storage/app/base-platform/quality-gates/` |

## Collection Process

1. **Bootstrap Timing**
   - Run quickstart end-to-end on supported profiles; record start/end timestamps in `bootstrap-timings.json`.
   - Include profile name, engineer initials, and parity validation outcome.

2. **Support Ticket Tracking**
   - Apply tag `baseline-support` to relevant helpdesk tickets.
   - Export counts per sprint and append summary to `support-metrics.log` (date, tickets opened, tickets resolved).
   - Annotate root causes and follow-up actions.

3. **CI Duration Monitoring**
   - Use GitHub Actions API to fetch P90 duration for `tests.yml` over the past 7 days.
   - Document values under the “SLA Results” section within `docs/base-platform/ci-policy.md`.
   - If P90 exceeds target, open a remediation task.

4. **Queue Throughput**
   - Capture Horizon or queue metrics snapshot monthly; store raw numbers in `queue-throughput.log`.
   - Note test scenario (load test or production observation) and any scaling adjustments.

5. **Dependency Review Runtime**
   - Execute `./scripts/automation/dependency-review.sh` (or run the commands individually) during the monthly governance window.
   - Confirm `storage/app/base-platform/dependency-reports/<yyyy-mm>-dependency-review.json` exists and reflects the latest catalogue.
   - Append the runtime summary to `storage/app/base-platform/dependency-performance.log`.
   - Create or update the governance issue using `.github/ISSUE_TEMPLATE/dependency-review.md`, including remediation tasks.

6. **Environment Support Validation**
   - Run `php artisan platform:validate-profiles --all` for native and container profiles.
   - Append validation outcomes to `storage/app/base-platform/environment-support.log` (include timestamps, profile, and result).
   - Cross-link updates to `docs/base-platform/environment-support-matrix.md`.
7. **Quality Gate Adoption**
   - Apply the pull request checklist documented in `docs/base-platform/quickstart.md`.
   - Weekly, export merged PRs, calculate checklist completion, and store a summary report in `storage/app/base-platform/quality-gates/<yyyy-mm-dd>-adoption.md`.
   - Record adoption trends in sprint retrospective notes and flag drops below the 95% target to Platform Engineering.

## Evidence & Reporting

- Monthly governance ticket attaches:
  - Latest bootstrap timing file
  - CI P90 summary
  - Queue throughput log excerpt
  - Support ticket delta table
  - Dependency review report (`storage/app/base-platform/dependency-reports/<yyyy-mm>-dependency-review.json`)
  - Dependency performance log entry (`storage/app/base-platform/dependency-performance.log`)
  - Environment support validation excerpt (`storage/app/base-platform/environment-support.log`)
- QA reviewers verify presence of artifacts during Phase 6 checkpoints (Tasks T078A, T079A, T081).
- Retain at least three months of historical data for trend analysis.

## Roles & Responsibilities

- **Platform Engineering**: Collects bootstrap timings, queue throughput, dependency governance evidence, and maintains `support-metrics.log`.
- **DevEx**: Monitors CI duration metrics and raises remediation tasks when SLA breached.
- **Support Team Liaison**: Ensures helpdesk tags applied and exports data each sprint.
- **Governance Lead**: Owns monthly dependency review artefacts and ensures remediation follow-up is tracked.

## Escalation

- If any metric misses target for two consecutive periods:
  - Create a remediation issue with owner, expected fix date, and link to evidence.
  - Include summary in the monthly dependency review performance report for visibility.

## Related References

- Spec §SC-001, §SC-002, §SC-005
- Plan §QA Deliverables & Evidence
- Tasks Phase 6 (T077A, T078A, T079A, T080, T081)
- CI Policy (`docs/base-platform/ci-policy.md`)

## Phase 6 Verification (2025-11-09)

- Confirmed bootstrap timing evidence updated with validation timestamp (`storage/app/base-platform/bootstrap-timings.json`).
- Confirmed queue throughput snapshot remains ≥1k jobs/min (log appended `2025-11-09T21:39Z`).
- Documented CI P90 outcome in `docs/base-platform/ci-policy.md#sla-results-2025-11-09`.
- Added quality gate adoption metric, PR checklist reference, and weekly reporting workflow (`storage/app/base-platform/quality-gates/`).
