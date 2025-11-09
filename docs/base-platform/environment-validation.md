# Environment Validation Workflow

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Purpose

- Guarantee native and container profiles remain in parity
- Surface drift before feature teams begin work
- Provide auditable validation artifacts for QA

## Commands

| Command | Description |
|---------|-------------|
| `php artisan platform:validate-profiles --profile=native` | Validate native profile only |
| `php artisan platform:validate-profiles --profile=container` | Validate container profile only |
| `php artisan platform:validate-profiles --all` | Validate both profiles (default) |

## Output

- Validation results logged to the console
- Issues listed under each profile when warnings/failures occur
- Reports persisted to `storage/app/base-platform/validation/` (CI attaches artifacts)

## Schedule

- Weekly (`platform:validate-profiles --all`) via Laravel scheduler (`bootstrap/app.php`)
- Triggered nightly in CI when parity job runs

## Escalation

1. Review generated report in `storage/app/base-platform/validation/`
2. If status is WARNING or FAIL, consult `docs/base-platform/bootstrap-recovery.md`
3. Escalate unresolved issues to Platform Engineering with report attachment
