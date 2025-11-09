# Dependency Governance Policy

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Purpose

- Document the ownership, classification, and lifecycle rules for backend and frontend dependencies referenced by the Base Platform.
- Provide the authoritative catalogue schema consumed by automation (`platform:dependency-review`, `platform:dependency-review-performance-report`).
- Describe monthly stewardship workflows, evidence storage, and escalation procedures when automation encounters failures.

## Catalogue Structure

- Source of truth: `storage/app/base-platform/dependencies.json`
- JSON schema per entry:

```json
{
  "name": "laravel/framework",
  "version": "12.0.0",
  "classification": "core", // core | optional | experimental
  "owner": "Platform Engineering",
  "justification": "Framework baseline",
  "lastReviewedAt": "2025-10-31",
  "notes": "Pin to LTS release"
}
```

- Owners must update `lastReviewedAt` during each monthly review and document decisions in the monthly report.
- Experimental dependencies require a linked ADR or spike reference in `notes`.

## Classification Rules

| Class | Definition | Expectations |
|-------|------------|--------------|
| Core | Required for baseline automation or runtime | Removal requires approval from Platform Engineering + DevEx, documented in catalogue and changelog |
| Optional | Enhances workflows but not mandatory for bootstrap or CI | May be toggled off; list fallback instructions in catalogue notes |
| Experimental | Under evaluation; not required for baseline success criteria | Must include exit criteria and review date; automation warns when review date passes |

## Monthly Review Workflow

1. Execute `php artisan platform:dependency-review` (or CI equivalent) to generate the monthly report.
2. Script `scripts/automation/dependency-review.sh` uploads outputs and opens/updates tracking issues.
3. Run `php artisan platform:dependency-review-performance-report` to capture:
   - Command runtime (seconds)
   - Success/failure state
   - Counts of outdated packages by severity (critical/high/medium/low)
   - Links to issues/tasks opened for remediation
4. Archive reports in `storage/app/base-platform/dependency-reports/` (create directory if missing) and attach to the monthly governance ticket.
5. Update `dependencies.json` entries updated during the review.

## Lockfile Maintenance

- When dependencies change:
  1. Run `composer update <package>` (or `composer install` for clean sync) and `bun install` to refresh `composer.lock` and `bun.lock`.
  2. Re-run `php artisan platform:bootstrap --profile=<profile>` and `composer workflow:core` to validate the environment.
  3. Record bootstrap time updates in `storage/app/base-platform/bootstrap-timings.json` if relevant.
  4. Capture dependency review artifacts (`platform:dependency-review`, performance report) before committing.
  5. Document notable version bumps or breaking changes in the monthly governance ticket.
- Pull request checklist:
  - Include diff of lockfiles and mention validation commands executed.
  - Link to CI run demonstrating green workflows.
  - Update [Toolchain Baseline](./toolchain-baseline.md) and quickstart prerequisites if runtime versions changed.

## Failure Handling

- If automation cannot reach package registries or create GitHub issues:
  - Re-run the command after resolving connectivity.
  - Manually log the failure in the monthly ticket with reproduction steps.
  - Notify Platform Engineering in `#ci-heavy-quality`.
- If severity counts exceed policy thresholds (critical or high outdated packages):
  - File a follow-up task with owner assignment.
  - Escalate to the next release gate until resolved.

## Evidence & Traceability

- Monthly reports: `storage/app/base-platform/dependency-reports/YYYY-MM.json`
- Performance logs: `storage/app/base-platform/dependency-performance.log`
- Catalogue snapshots: Git-committed `dependencies.json`
- Phase Checkpoint: Aligns with Tasks T070–T075A in Phase 5. Confirm this policy doc and evidence artifacts exist before marking the Phase 5 checkpoint complete.
- Tracking ticket template: `.github/ISSUE_TEMPLATE/dependency-review.md`

## Ownership & Contacts

- Platform Engineering: Maintains catalogue, review automation, and performance report command.
- DevEx: Monitors CI jobs and ensures report artifacts upload correctly.
- Documentation updates (this policy): Update when classification rules, automation scripts, or reporting cadence changes.

## Related References

- Spec §FR-003, §FR-009
- Plan §Operational Cadence & Monitoring
- Tasks Phase 5 (T068–T075A)
- Scripts: `scripts/automation/dependency-review.sh`
- Commands: `php artisan platform:dependency-review`, `php artisan platform:dependency-review-performance-report`
