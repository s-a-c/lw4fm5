Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Credential Onboarding Checklist

## Objective

Provide a lightweight flow for onboarding additional engineers while maintaining parity with the solo-developer baseline.

## Required Secrets

- `FLUX_API_TOKEN`
- `FLUX_API_SECRET`
- `PLAYWRIGHT_SERVICE_TOKEN`

## Steps

1. Request access via Platform Engineering (`platform-support` Slack)
2. Platform Engineering provisions secrets and updates credential log
3. Developer stores secrets in 1Password and updates `.env.<profile>`
4. Run `./scripts/profile/use-<profile>.sh` to sync `.env`
5. Execute `php artisan platform:bootstrap --profile=<profile>`
6. Run `php artisan platform:parity-check --profile=<profile>` to verify parity

## Output Artifacts

- Updated `.env.native` / `.env.container` (encrypted via `php artisan env:encrypt`)
- GitHub Actions secrets confirmed via `gh secret list`
- Validation reports stored under `storage/app/base-platform/validation/`
