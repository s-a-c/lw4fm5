# Credential Onboarding Checklist

Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

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

## Composer Authentication

- Private repositories:
  - `https://composer.fluxui.dev` (Flux Pro packages)
  - `https://satis.spatie.be` (Spatie packages)
- Required credentials:
  - `FLUX_COMPOSER_USERNAME` / `FLUX_COMPOSER_TOKEN` (scoped to Flux Pro downloads)
  - `SPATIE_COMPOSER_TOKEN`
- Local setup:
  1. Create `~/.config/composer/auth.json` (or project-level `composer-auth.json`) with tokens:

     ```json
     {
       "http-basic": {
         "composer.fluxui.dev": {
           "username": "FLUX_COMPOSER_USERNAME",
           "password": "FLUX_COMPOSER_TOKEN"
         }
       },
       "bearer": {
         "satis.spatie.be": "SPATIE_COMPOSER_TOKEN"
       }
     }
     ```

  2. Run `composer config --global --auth http-basic.composer.fluxui.dev <user> <token>` if you prefer CLI entry.
  3. Validate with `composer install --no-scripts` (should fetch private packages without prompting).
- CI configuration:
  - Store tokens in GitHub Actions secrets (`COMPOSER_FLUX_USERNAME`, `COMPOSER_FLUX_TOKEN`, `COMPOSER_SPATIE_TOKEN`).
  - `composer install` steps inject credentials via `COMPOSER_AUTH` environment variable (see `.github/workflows/tests.yml`).
- Recovery:
  - If bootstrap fails with 401/403, confirm tokens have not expired and re-run steps above.
