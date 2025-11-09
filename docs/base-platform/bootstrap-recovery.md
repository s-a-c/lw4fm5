# Bootstrap Recovery Playbook

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Purpose

- Provide a repeatable path when the bootstrap workflow fails
- Reduce mean time to recovery for native and container profiles
- Document escalation points so failures never stall onboarding

## Common Failure Modes

| Symptom | Detection | Resolution |
|---------|-----------|------------|
| Missing Flux credentials | `platform:bootstrap` exits with code 2 and reports the missing secret | Follow [Credential Onboarding](./credential-onboarding.md) to request access and populate `.env.native` / GitHub Actions secrets |
| Offline/proxied network | Bootstrap logs include `mirror required` | Mirror private registries per [Offline & Proxy Guide](./offline-proxy.md) and rerun bootstrap |
| Stale vendor cache | Bootstrap logs show composer autoload errors | Re-run with `--force-clean` to clear vendor/node_modules before executing |
| Queue smoke test failure | Command output mentions `queue smoke test failed` | Confirm Redis is running, restart queue worker, rerun bootstrap |

## Recovery Workflow

1. Capture the last 200 lines of bootstrap output (`storage/logs/bootstrap.log`)
2. Match the failure symptom using the table above
3. Apply the documented fix
4. Rerun `php artisan platform:bootstrap --profile=<profile>`
5. If failure persists, escalate to Platform Engineering with log excerpts and timestamp

## Escalation

- **Primary:** Platform Engineering Slack `#platform-support`
- **Secondary:** Create incident ticket, attach log snippets from `storage/logs/bootstrap.log`, tag `platform-engineering`
- Phase Checkpoint: Required for Phase 3 completion—ensure this playbook is current and linked in checkpoints before advancing to Phase 4 or later phases.
