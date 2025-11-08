Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Data Model: Base Platform Foundation

<details>
<summary>Expand for Table of Contents</summary>

- [Data Model: Base Platform Foundation](#data-model-base-platform-foundation)
  - [1. Overview](#1-overview)
  - [2. Entities](#2-entities)
    - [2.1. environment\_profiles](#21-environment_profiles)
    - [2.2. toolchain\_definitions](#22-toolchain_definitions)
    - [2.3. credential\_policies](#23-credential_policies)
    - [2.4. workflow\_suites](#24-workflow_suites)
    - [2.5. parity\_results](#25-parity_results)
  - [3. Relationships \& Lifecycle](#3-relationships--lifecycle)
  - [4. Validation Rules](#4-validation-rules)

</details>

---

## 1. Overview

Although the feature introduces primarily operational workflows, several configuration entities require structured storage (database tables, configuration files, or JSON metadata) to keep the baseline auditable, scriptable, and testable. Entities below can live in database tables, configuration JSON, or structured YAML/JSON stored in the repository and loaded via config caches.

## 2. Entities

### 2.1. environment_profiles

| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| name | string | `"container"`, `"native"` |
| runtime_versions | jsonb | Records PHP, Bun, Node (if fallback), Redis, Postgres versions |
| prerequisites | jsonb | OS-level dependencies, virtualization requirements |
| smoke_check_script | string | Reference to automated smoke test command |
| status | enum(`supported`,`deprecated`) | Only supported profiles exposed in docs |
| created_at / updated_at | timestamps | Track lifecycle |

### 2.2. toolchain_definitions

| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| language | string | `"php"`, `"javascript"`, `"ci"` |
| version | string | Semantic version with constraint (e.g., `^8.5`) |
| enforcement_scope | enum(`local`,`ci`,`both`) | Used when validating parity |
| verification_command | string | CLI snippet run during parity checks |
| documentation_url | string nullable | Reference for contributors |
| created_at / updated_at | timestamps | |

### 2.3. credential_policies

| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| context | enum(`ci`,`local`) | Scope of the secret |
| storage_mechanism | enum(`github_actions_secret`,`encrypted_env_file`) | Current baseline with future expansion |
| rotation_interval_days | integer | Default 90-day cadence |
| owner | string | Responsible person/team (solo developer for now) |
| notes | text nullable | Future migration instructions |
| created_at / updated_at | timestamps | |

### 2.4. workflow_suites

| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| name | string | `"core-quality"`, `"heavy-quality"` |
| triggers | jsonb | Lists push/nightly/release conditions |
| required_checks | jsonb | Lists named jobs (lint, unit, type, mutation, browser) |
| sla_minutes | integer | Target completion SLA (e.g., 25 for core) |
| created_at / updated_at | timestamps | |

### 2.5. parity_results

| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| environment_profile_id | uuid FK → environment_profiles.id | |
| run_date | timestamp | Execution time |
| status | enum(`pass`,`fail`,`warning`) | Overall parity outcome |
| issues | jsonb | Detailed parity differences (versions, missing services) |
| created_at | timestamp | Record insertion |

### 2.6. workflow_suite_channels

| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| workflow_suite_id | uuid FK → workflow_suites.id | Supports many channels per suite |
| channel | string | Destination identifier (Slack webhook alias, email list, etc.) |
| medium | enum(`slack`,`email`,`webhook`) | Delivery mechanism |
| created_at / updated_at | timestamps | Track maintenance changes |

## 3. Relationships & Lifecycle

- `environment_profiles` 1→N `parity_results` to track parity drift over time.
- `toolchain_definitions` link logically to `environment_profiles` (many-to-many) when validating runtime parity.
- `workflow_suites` reference `toolchain_definitions` to ensure CI scripts use the correct versions.
- `credential_policies` reference both CI pipelines and local docs; future multi-user support can add a join table mapping developers to access grants.
- `workflow_suites` reference `workflow_suite_channels` (1→N) ensuring each suite can alert multiple destinations without duplicating suite metadata.
- State transitions: profiles move from `supported` → `deprecated` when superseded; workflows update `sla_minutes` as optimization efforts land.

## 4. Validation Rules

- Version fields must pass semantic version validation and align with lockfiles checked into the repo.
- Parity results marked `fail` trigger incident response workflow and must include at least one issue entry.
- Credential policies require rotation interval ≥30 days; alerts should surface as part of maintenance scripts.
- Workflow suites must define at least one trigger and one required check; nightly suites add release-gate requirement flags.
