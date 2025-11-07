Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Quickstart: Base Platform Foundation

## Prerequisites
- macOS 15+ (Herd/native path) or Docker Desktop 4.33+ (container path)
- PHP 8.5 with required extensions (Herd or Homebrew bundle)
- Bun ≥1.1.0 (installed globally)
- Composer 2.7+
- PostgreSQL 15+, Redis 7+
- GitHub CLI (for fetching Actions secrets when onboarding additional contributors)
- QA verification: confirm GitHub Actions secrets exist (`gh secret list`), Bun ≥1.1 is installed, Docker or Herd is running, and encrypted `.env` values have been provisioned.

## 1. Clone & Environment Selection
1. `git clone git@github.com:lw4fm5/app.git`
2. Choose a profile:
   - **Native**: `cp .env.example .env.native && ./scripts/profile/use-native.sh`
   - **Container**: `cp .env.example .env.container && ./scripts/profile/use-container.sh`
3. Export `BASE_PLATFORM_PROFILE=native` (or `container`).

## 2. Bootstrap Workflow
```bash
composer setup -- --profile=${BASE_PLATFORM_PROFILE}
```
The command performs:
- Composer install + private repository auth prompt
- Bun install + asset build
- Database migrations & seeders
- Queue worker smoke test
- Asset compilation verification
- Bootstrap recovery helper registration

If Flux credentials are missing, the script exits with actionable guidance (also see `docs/base-platform/bootstrap-recovery.md` and `docs/base-platform/credential-onboarding.md`).

## 3. Parity Smoke Test (Optional but Recommended)
```bash
php artisan platform:parity-check --profile=${BASE_PLATFORM_PROFILE}
```
Outputs any version drift, missing services, or failing smoke tests. Resolve before continuing. Consult `docs/base-platform/offline-proxy.md` when operating behind restricted networks.

To run the full environment validation that covers both profiles:
```bash
php artisan platform:validate-profiles --all
```
See `docs/base-platform/environment-validation.md` for interpreting results.

## 4. Run Quality Gates
```bash
composer lint
composer test
```
Ensure `composer test` triggers lint, unit, type, and security audit pipelines. Mutation/browser suites run nightly; to execute on-demand:
```bash
composer test:mutation
bun run test:browser
```

To verify policy acknowledgement compliance locally:
```bash
php artisan policy:checksum-monitor --once
```

## 5. Start Dev Environment
- Native profile: `composer dev`
- Container profile: `./vendor/bin/sail up`

Verify:
- `http://localhost:8000` responds
- Horizon dashboard accessible
- `bun run dev` (via `composer dev`) hot-reloads assets

## 6. Credential Checklist (Solo Developer)
- Store Flux credentials and other secrets in encrypted local `.env` (1Password/Keychain notes)
- Verify GitHub Actions secrets for Flux credentials exist (`gh secret list`)
- Review rotation playbook (`docs/base-platform/credential-rotation.md`) and onboarding checklist (`docs/base-platform/credential-onboarding.md`).

## 7. Observability Hooks
- `php artisan platform:health` ensures queue, scheduler, asset pipeline checks pass
- Metrics exported via Prometheus endpoint `/metrics`; confirm scrape success in local stack
- Policy checksum monitor scheduled nightly; confirm recent run in CI logs or via `php artisan policy:checksum-monitor --once`
- Environment validation command scheduled weekly; confirm latest run in CI workflow results or via `php artisan platform:validate-profiles --once`

## 8. QA Validation Workflow
- Run native validation: `php artisan platform:validate-profiles --profile=native` and archive the report in `storage/app/base-platform/validation/`.
- Run container validation: `php artisan platform:validate-profiles --profile=container` and archive alongside the native report.
- Execute checksum monitor dry run: `php artisan policy:checksum-monitor --once`; store output together with validation reports.
- Confirm nightly heavy-suite and validation jobs passed in GitHub Actions before release tagging.
- Record support-ticket metrics before launch and weekly after launch following `docs/base-platform/support-metrics.md`.

## 9. CI Verification
Push a branch; ensure GitHub workflows `lint`, `tests`, and `browser-tests` complete in <25 minutes. Heavy suites execute nightly—monitor Actions dashboard for results before tagging releases.

## 10. Updating Tooling
- Run `composer update:requirements` monthly (opens PR with toolchain bumps)
- Review parity test output; if drift detected, align native/container scripts immediately

## Troubleshooting
- **Bootstrap failure**: follow `docs/base-platform/bootstrap-recovery.md`
- **Offline or proxied networks**: mirror registries per `docs/base-platform/offline-proxy.md`
- **Missing secrets**: update encrypted `.env` and rerun setup; see `docs/base-platform/credential-onboarding.md`
- **Bun missing**: reinstall via `curl -fsSL https://bun.sh/install | bash`
- **Docker resources**: allocate ≥6GB RAM for container profile
- **Playwright cache issues**: delete `~/.cache/ms-playwright` and re-run `bunx playwright install --with-deps`
- **Mutation test slowness**: run with `INFECTION_THREADS=2` locally or rely on nightly CI
