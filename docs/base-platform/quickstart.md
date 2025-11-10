# Base Platform Quickstart

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## 0. Tooling Prerequisites

- PHP `^8.5`, Composer `^2.7`, Bun `>=1.1.0`, Node `>=25` (see [Toolchain Baseline](./toolchain-baseline.md)).
- Podman Desktop/`podman machine` v5.7.0+ (preferred) with virtualization enabled. Docker Desktop may be used only when Podman support is unavailable on the host.
- **Install Podman (preferred)**
  - **macOS (Apple Silicon)**: Install Podman Desktop v5.7.0+ and enable the default `podman machine` (`podman machine init --now`). Confirm virtualization is enabled in macOS Settings > Privacy & Security > Developer Tools (see the [Podman release notes](https://github.com/containers/podman/releases) for platform packages).
  - **Windows 11 (WSL2)**: Install Podman Desktop v5.7.0+ with WSL integration. From the Ubuntu 24.04 WSL shell, run `podman machine init --now` and verify `podman info` succeeds. Ensure the store repository is shared via `\\wsl$` for Herd path parity.
  - **Ubuntu 24.04**: Install `podman` from the official repositories (`sudo apt install podman`), enable system service (`systemctl --user enable --now podman.socket`), and verify rootless containers with `podman run --rm hello-world`.
- **Fallback Docker (if Podman unavailable)**: Install Docker Desktop ≥4.33 (macOS/Windows) or Docker Engine ≥26 (Linux). Mirror the Podman steps above to ensure the container profile commands operate identically.
- **Herd integration**: Herd continues to manage the native PHP runtime. Confirm Herd-installed PHP 8.5 remains the default when using the native profile, and that Podman/Docker is reserved for the container profile to avoid PATH conflicts.
- **Composer authentication & secrets**: Configure private composer credentials following [Credential Onboarding](./credential-onboarding.md#composer-authentication) and ensure required GitHub Actions secrets exist (`COMPOSER_FLUX_USERNAME`, `COMPOSER_FLUX_TOKEN`, `COMPOSER_SPATIE_TOKEN`, etc.).
- **Prometheus setup**: Install Prometheus ≥2.53 from [prometheus.io](https://prometheus.io/) or via package manager (`brew install prometheus`, `choco install prometheus`, `sudo apt install prometheus`). Use the generated `config/prometheus/base-platform.yml` scrape configuration (Phase 3 tasks) and run `prometheus --config.file=config/prometheus/base-platform.yml`. Confirm `/metrics` endpoints from bootstrap, parity, and validation commands are reachable (see [Observability Setup](./observability.md)).
- **Grafana setup**: Install Grafana OSS ≥11.2 from [grafana.com/get](https://grafana.com/get/#:~:text=Cloud-,OSS,-Grafana) or via package manager (`brew install grafana`, `choco install grafana`, `sudo apt install grafana`). Import dashboards located in `docs/base-platform/observability/grafana/` and point the Prometheus data source to `http://localhost:9090` to visualize bootstrap SLA, parity drift counts, and CI success metrics (detailed steps in [Observability Setup](./observability.md)).

## 1. Select a Profile

```bash
./scripts/profile/use-native.sh      # Herd / local runtime
# or
./scripts/profile/use-container.sh   # Podman/Docker runtime
```

Verify `.env` is symlinked to the chosen profile and `BASE_PLATFORM_PROFILE` is exported.
> **Windows via WSL**: Launch these commands from the Ubuntu 24.04 WSL shell. Ensure Podman Desktop (or Docker Desktop if used) has WSL integration enabled so the container profile maps to the same environment used in CI.

## 2. Run Bootstrap

```bash
php artisan platform:bootstrap --profile=${BASE_PLATFORM_PROFILE}
```

Use `--force-clean` to purge caches if you encounter stale vendor or node modules. Any missing secret will reference [Credential Onboarding](./credential-onboarding.md).
> Manual re-runs: The bootstrap command wraps `composer install`, `bun install`, database migrations, and asset builds. Run `composer install` and `bun install` explicitly if you need to verify dependency setup outside the bootstrap flow.

## 3. Validate Parity

```bash
php artisan platform:parity-check --profile=${BASE_PLATFORM_PROFILE}
```

Review output; PASS status indicates no drift. WARNING or FAIL statuses include remediation steps.

## 4. Weekly Validation

```bash
php artisan platform:validate-profiles --all
```

Validation runs weekly via scheduler and nightly in CI, but you can execute it locally after large changes. Results are stored in `storage/app/base-platform/validation/`.

## 5. CI Parity Checks

```bash
composer workflow:core
php artisan policy:checksum-monitor --once
```

Validate that the same suite executed in CI passes locally before opening a pull request.

## 6. Recovery & Support

- Bootstrap failure? See [Bootstrap Recovery](./bootstrap-recovery.md)
- Offline network? Follow [Offline & Proxy Guide](./offline-proxy.md)
- Credential questions? Read [Credential Onboarding](./credential-onboarding.md) and [Credential Rotation](./credential-rotation.md)
- Support metrics evidence? Review [Support Metrics & Evidence Plan](./support-metrics.md) before Phase 6 checkpoints.

## 7. Package Installation Commands & Maintenance

- **Clean install (native or container):**

  ```bash
  composer install
  bun install
  composer setup -- --profile=${BASE_PLATFORM_PROFILE}
  ```

  (bootstrap invokes these automatically; run manually when debugging or upgrading.)
- **WSL guidance:** Execute commands from the Ubuntu 24.04 WSL shell. Ensure Podman Desktop (or Docker Desktop fallback) shares the repository via `\\wsl$` so `bun install` and Composer cache behave consistently.
- **Upgrade workflow:**
  1. Update dependencies (`composer update vendor/package`, `bun install`) as needed.
  2. Re-run `php artisan platform:bootstrap --profile=<profile>` to confirm the environment.
  3. Update lockfiles (`composer.lock`, `bun.lock`) and follow the [Lockfile Maintenance](./dependency-policy.md#lockfile-maintenance) checklist.
  4. Run targeted tests (`composer workflow:core`, `composer workflow:heavy` if required).
- **CI cache rehydration:** Nightly workflows call `bunx playwright install --with-deps` and `composer install` with cache keys. If cache misses increase, re-run `composer install` / `bun install` locally and commit the updated lockfiles.

## 8. Monthly Dependency Stewardship

```bash
./scripts/automation/dependency-review.sh
```

- Generates `storage/app/base-platform/dependency-reports/<yyyy-mm>-dependency-review.json`.
- Appends runtime metrics to `storage/app/base-platform/dependency-performance.log`.
- Use `.github/ISSUE_TEMPLATE/dependency-review.md` to open the governance issue and attach both artefacts.
- Update `storage/app/base-platform/environment-support.log` if profile validation uncovered new notes during the review.

## 9. Phase 6 Validation Snapshot (2025-11-09)

- Native profile bootstrap completed in **19.9 minutes**; container profile completed in **22.4 minutes** (see `storage/app/base-platform/bootstrap-timings.json`, validated `2025-11-09T21:39Z`).
- Parity audit logged for both profiles with **PASS** status (see `storage/app/base-platform/parity-report.log`, verification note appended `2025-11-09T21:39Z`).
- CI core workflow P90 recorded at **22.6 minutes** over the past 7 days (documented in `docs/base-platform/ci-policy.md#sla-results-2025-11-09`).
- Queue throughput snapshot captured at **1,180 jobs/min** with zero failures (see `storage/app/base-platform/queue-throughput.log`, verification appended `2025-11-09T21:39Z`).
- Targeted Pest runs (`php artisan test` for bootstrap, parity, metrics, and policy-monitor suites) confirmed automation remains green prior to checklist sign-off.
- All evidence cross-referenced in Phase 6 checklists prior to marking tasks complete.

## 10. Pull Request Quality-Gate Checklist

Include the following block in every pull request description and complete it before requesting review. Failure to check an item must include a short explanation.

```
- [ ] Lint scripts (`composer lint`, `bun run lint`) executed and green
- [ ] Static analysis / type checks (`composer analyse`) completed
- [ ] Automated tests (`composer test` or targeted `php artisan test`) executed
- [ ] Browser or workflow-specific suites (if applicable) executed or scheduled
- [ ] Baseline evidence attached or linked (logs, metrics, artifacts)
```

- QA audits adoption weekly by extracting merged PRs and computing checklist completion rate. Store the summary report (CSV or Markdown) in `storage/app/base-platform/quality-gates/` and reference the timestamp in the sprint retro notes.
