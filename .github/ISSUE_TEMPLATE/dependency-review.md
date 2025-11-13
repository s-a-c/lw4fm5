---
name: Monthly Dependency Review
about: Capture results of the monthly dependency stewardship workflow (FR-009)
title: "[Dependency Review] YYYY-MM report"
labels: ["dependency", "governance"]
assignees: ["platform-engineering"]
---

Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Summary

- Report timestamp: `{{ env.REVIEW_TIMESTAMP }}`
- Runtime (seconds): `{{ env.REVIEW_RUNTIME }}`
- Status: `{{ env.REVIEW_STATUS }}`

## Severity Counts

- Critical: `{{ env.REVIEW_CRITICAL }}`
- High: `{{ env.REVIEW_HIGH }}`
- Medium: `{{ env.REVIEW_MEDIUM }}`
- Low: `{{ env.REVIEW_LOW }}`

## Required Attachments

- [ ] `storage/app/base-platform/dependency-reports/{{ env.REPORT_FILE }}`
- [ ] `storage/app/base-platform/dependency-performance.log`
- [ ] Updated `storage/app/base-platform/dependencies.json` (if changes applied)
- [ ] Supporting notes in [Support Metrics & Evidence Plan](../../docs/base-platform/support-metrics.md)

## Follow-up Tasks

- [ ] Create remediation task(s) for outstanding advisories (critical/high)
- [ ] Schedule dependency upgrade PRs where required
- [ ] Update `docs/base-platform/dependency-policy.md` with decisions made

## Additional Notes

- Overdue catalogue entries: `{{ env.REVIEW_OVERDUE }}`
- Issue created via: `scripts/automation/dependency-review.sh`
