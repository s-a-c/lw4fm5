Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Environment Support Matrix

The matrix documents the supported local development environments and verifies that both native and containerized profiles remain in parity. Each row lists the runtime versions used during the most recent validation along with the automated checks that must pass. Update the table whenever prerequisites change and archive validation logs at `storage/app/base-platform/environment-support.log`.

| Profile | OS & Version | Runtime Versions | Validation Command | Automated Checks | Notes |
|---------|--------------|------------------|--------------------|------------------|-------|
| Native  | macOS 15.x (Apple Silicon) | PHP 8.5.0, Bun 1.1.x, Node 22.x (fallback), Composer 2.7.x, PostgreSQL 16.x, Redis 7.x | `php artisan platform:bootstrap --profile=native` | Smoke tests, queue health, asset build parity, policy checksum | Requires Xcode CLI tools, Homebrew, and Docker Desktop for optional services |
| Native  | Ubuntu 24.04 LTS | PHP 8.5.0, Bun 1.1.x, Node 22.x (fallback), Composer 2.7.x, PostgreSQL 16.x, Redis 7.x | `php artisan platform:bootstrap --profile=native` | Smoke tests, queue health, asset build parity, policy checksum | Validate SQLite fallback for quick checks; ensure `libpq` headers installed |
| Container | Windows 11 (WSL2 Ubuntu 24.04) | Laravel Sail container images (PHP 8.5.0, Bun 1.1.x) | `php artisan platform:bootstrap --profile=container` (run from WSL) | Container parity check, queue/worker readiness, asset build parity, policy checksum | Enable WSL2, install Ubuntu 24.04 distro, ensure Docker Desktop with WSL integration; share repo via `\\wsl$` path |
| Container | macOS 15.x (Apple Silicon) | Laravel Sail container images (PHP 8.5.0, Bun 1.1.x) | `php artisan platform:bootstrap --profile=container` | Container parity check, queue/worker readiness, asset build parity, policy checksum | Docker Desktop ≥ 4.33 with Apple Virtualization Framework |
| Container | Ubuntu 24.04 LTS | Laravel Sail container images (PHP 8.5.0, Bun 1.1.x) | `php artisan platform:bootstrap --profile=container` | Container parity check, queue/worker readiness, asset build parity, policy checksum | Requires Docker Engine ≥ 26; ensure cgroup v2 enabled |

## Validation Workflow

1. Run the profile switch script (`scripts/profile/use-native.sh` or `scripts/profile/use-container.sh`). On Windows, execute the container profile from the WSL shell.
2. Execute the bootstrap command indicated above.
3. Confirm the automated checks complete successfully and metrics are emitted.
4. Append the validation summary and timestamps to `storage/app/base-platform/environment-support.log`.
5. File an issue if any check fails or parity differences are detected; Block releases until parity is restored.

## Change Management

- Update this document when supported OS or runtime versions change.
- Record validation evidence in the log file whenever monthly dependency stewardship tasks run.
- Coordinate updates with `docs/base-platform/toolchain-baseline.md` and `docs/base-platform/environment-validation.md` to maintain consistency.
