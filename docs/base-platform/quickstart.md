Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Base Platform Quickstart

## 1. Select a Profile

```bash
./scripts/profile/use-native.sh      # Herd / local runtime
# or
./scripts/profile/use-container.sh   # Docker-based runtime
```

Verify `.env` is symlinked to the chosen profile and `BASE_PLATFORM_PROFILE` is exported.

## 2. Run Bootstrap

```bash
php artisan platform:bootstrap --profile=${BASE_PLATFORM_PROFILE}
```

Use `--force-clean` to purge caches if you encounter stale vendor or node modules. Any missing secret will reference [Credential Onboarding](./credential-onboarding.md).

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

## 5. Recovery & Support

- Bootstrap failure? See [Bootstrap Recovery](./bootstrap-recovery.md)
- Offline network? Follow [Offline & Proxy Guide](./offline-proxy.md)
- Credential questions? Read [Credential Onboarding](./credential-onboarding.md) and [Credential Rotation](./credential-rotation.md)
