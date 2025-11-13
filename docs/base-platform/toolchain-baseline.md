# Toolchain Baseline

Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

> Record the authoritative runtime versions used by the Base Platform bootstrap, parity, and CI automation. Update this table whenever a version bump lands in composer.lock, bun.lockb, or workflow configuration.

| Component | Version | Source of Truth | Validation Command |
|-----------|---------|-----------------|--------------------|
| PHP | 8.5.x | `composer.json` / Herd profile | `php -v` |
| Bun | ≥1.1.0 | `package.json` engines | `bun --version` |
| Node (fallback) | ≥25 | `package.json` engines | `node --version` |
| Composer | 2.7.x | Developer machine | `composer --version` |
| Podman | 5.7.x | Podman Desktop / `podman machine` | `podman version` |
| Docker (fallback) | ≥4.33 Desktop / ≥26 Engine | Docker Desktop or Engine | `docker --version` |
| PostgreSQL | 18.x | Podman/Docker Compose / native install | `psql --version` |
| Redis | 7.x | Podman/Docker Compose / native install | `redis-server --version` |
| Playwright | 1.56.x | `package.json` devDependencies | `bunx playwright --version` |
| Prometheus | ≥2.53 | `config/prometheus/base-platform.yml` | `prometheus --version` |
| Grafana OSS | ≥11.2 | `docs/base-platform/observability/grafana/` | `grafana-server --version` |

## Update Procedure

1. Bump the version in the appropriate lockfile(s) and configuration
2. Re-run `composer setup -- --profile=<profile>` to confirm parity
3. Update this document, the quickstart prerequisites, observability setup guide, and any related tasks
4. Commit changes and attach QA validation evidence per Phase 6 tasks
