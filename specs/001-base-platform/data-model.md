Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

# Data Model: Base Platform Foundation

## Overview

Although the feature introduces primarily operational workflows, several configuration entities require structured storage (database tables, configuration files, or JSON metadata) to keep the baseline auditable, scriptable, and testable. Entities below can live in database tables, configuration JSON, or structured YAML/JSON stored in the repository and loaded via config caches.

## Entities

### environment_profiles
| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| name | string | `"container"`, `"native"` |
| runtime_versions | jsonb | Records PHP, Bun, Node (if fallback), Redis, Postgres versions |
| prerequisites | jsonb | OS-level dependencies, virtualization requirements |
| smoke_check_script | string | Reference to automated smoke test command |
| status | enum(`supported`,`deprecated`) | Only supported profiles exposed in docs |
| created_at / updated_at | timestamps | Track lifecycle |

### toolchain_definitions
| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| language | string | `"php"`, `"javascript"`, `"ci"` |
| version | string | Semantic version with constraint (e.g., `^8.5`) |
| enforcement_scope | enum(`local`,`ci`,`both`) | Used when validating parity |
| verification_command | string | CLI snippet run during parity checks |
| documentation_url | string nullable | Reference for contributors |
| created_at / updated_at | timestamps | |

### credential_policies
| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| context | enum(`ci`,`local`) | Scope of the secret |
| storage_mechanism | enum(`github_actions_secret`,`encrypted_env_file`) | Current baseline with future expansion |
| rotation_interval_days | integer | Default 90-day cadence |
| owner | string | Responsible person/team (solo developer for now) |
| notes | text nullable | Future migration instructions |
| created_at / updated_at | timestamps | |

### workflow_suites
| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| name | string | `"core-quality"`, `"heavy-quality"` |
| triggers | jsonb | Lists push/nightly/release conditions |
| required_checks | jsonb | Lists named jobs (lint, unit, type, mutation, browser) |
| sla_minutes | integer | Target completion SLA (e.g., 25 for core) |
| notification_channel | string | Slack/email hook for failures |
| created_at / updated_at | timestamps | |

### parity_results
| Field | Type | Rules |
|-------|------|-------|
| id | uuid | Primary identifier |
| environment_profile_id | uuid FK → environment_profiles.id | |
| run_date | timestamp | Execution time |
| status | enum(`pass`,`fail`,`warning`) | Overall parity outcome |
| issues | jsonb | Detailed parity differences (versions, missing services) |
| created_at | timestamp | Record insertion |

## Relationships & Lifecycle

- `environment_profiles` 1→N `parity_results` to track parity drift over time.
- `toolchain_definitions` link logically to `environment_profiles` (many-to-many) when validating runtime parity.
- `workflow_suites` reference `toolchain_definitions` to ensure CI scripts use the correct versions.
- `credential_policies` reference both CI pipelines and local docs; future multi-user support can add a join table mapping developers to access grants.
- State transitions: profiles move from `supported` → `deprecated` when superseded; workflows update `sla_minutes` as optimization efforts land.

## Validation Rules

- Version fields must pass semantic version validation and align with lockfiles checked into the repo.
- Parity results marked `fail` trigger incident response workflow and must include at least one issue entry.
- Credential policies require rotation interval ≥30 days; alerts should surface as part of maintenance scripts.
- Workflow suites must define at least one trigger and one required check; nightly suites add release-gate requirement flags.
