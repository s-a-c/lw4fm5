# Offline & Proxy Bootstrap Guide

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Goal

Ensure developers operating behind restrictive proxies or fully offline environments can complete the bootstrap workflow without drift.

## Mirror Strategy

1. Mirror Composer repositories using `composer config -g repositories.flux composer https://mirror.example.com/flux`
2. Mirror private npm packages via Artifactory or Verdaccio
3. Cache Bun dependencies with `bun install --cache-dir ~/.cache/bun`
4. Sync Playwright browser binaries using `bunx playwright install --with-deps --target=./offline-cache`

## Configuration Steps

- Update `.env.<profile>` with `COMPOSER_MIRROR_URL` and `NPM_REGISTRY_URL`
- Export `BOOTSTRAP_MIRROR_DIR` before running `platform:bootstrap`
- Share mirror configuration in the team wiki to keep parity

## Validation

- Run `php artisan platform:parity-check --profile=<profile>` after mirroring
- Expect a PASS status; any WARN/FAIL should be escalated with mirror logs

## Escalation

If mirrors produce inconsistent artifacts:

1. Capture checksum differences from `storage/app/base-platform/validation/`
2. Notify Platform Engineering with mirror URLs and timestamps
