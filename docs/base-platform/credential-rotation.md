Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Credential Rotation Playbook

## Scope

- Flux deployment credentials
- GitHub Actions environment secrets
- Local encrypted `.env` files for native and container profiles

## Cadence

| Context | Interval | Owner |
|---------|----------|-------|
| CI | 90 days | Platform Engineering |
| Local | 120 days | Solo Developer (handoff to Platform Engineering once team expands) |

## Rotation Steps

1. Generate new credential in Flux
2. Update GitHub secret via `gh secret set FLUX_API_TOKEN --body "<token>" --env production`
3. Update local `.env.<profile>` using `php artisan env:encrypt`
4. Record rotation in the credential log (1Password secure note)
5. Rerun `php artisan platform:bootstrap --profile=<profile>` to confirm success

## Verification

- `php artisan platform:parity-check --profile=<profile>` returns PASS
- `php artisan policy:checksum-monitor --once` reports no drift
- Queue and scheduler smoke tests remain green
