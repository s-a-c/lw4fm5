# Data Model: Social Areas Provisioning Phase 1

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Overview

- All tables live in the Storefront service PostgreSQL database.
- Foreign keys reference `users.id`; guests without accounts are represented by email until registration.
- UUID primary keys for externally referenced records (`invitations`, `access_logs`).
- Timestamps use `timezone('UTC', now())` defaults; soft deletes disabled for audit tables.

## Entities

### areas (seeded reference)

| Column | Type | Rules |
| --- | --- | --- |
| id | bigint (PK) | Auto-increment seed data (1=lobby, 2=greenroom, 3=residence) |
| slug | varchar(32) | Unique; values: `lobby`, `greenroom`, `residence` |
| name | varchar(64) | Display label |
| access_policy | jsonb | Serialized policy metadata (e.g., `{"requires_auth":true}`) |
| created_at / updated_at | timestamps | Seeded defaults |

**Notes**: No CRUD in Phase 1; used for joins and audit attribution.

### rooms

| Column | Type | Rules |
| --- | --- | --- |
| id | uuid (PK) | `Str::uuid()` |
| resident_id | bigint FK → users.id | Indexed (`rooms_resident_id_type_unique`) |
| type | enum(`sanctuary`,`parlour`,`den`) | Unique per resident |
| title | varchar(64) | Editable label for UI |
| share_mode | enum(`resident_only`,`resident_and_guests`) | Sanctuary forced to `resident_only`, den forced to `resident_only` |
| notes | text nullable | Optional description |
| created_at / updated_at | timestamps | |

**Relationships**: `rooms` belongsTo `users` (resident); hasMany `room_permissions`; soft delete not enabled (audit retains access via logs).

### room_permissions

| Column | Type | Rules |
| --- | --- | --- |
| id | uuid (PK) | |
| room_id | uuid FK → rooms.id | Indexed |
| subject_type | enum(`guest`,`resident`) | |
| subject_id | bigint nullable | For resident overrides |
| guest_email | varchar(255) nullable | Required when `subject_type=guest` |
| granted_by | bigint FK → users.id | Resident who granted access |
| created_at | timestamp | |

**Purpose**: Captures parlour sharing overrides. Sanctuary and den never expose rows.

### invitations

| Column | Type | Rules |
| --- | --- | --- |
| id | uuid (PK) | `Str::uuid()` |
| host_id | bigint FK → users.id | Indexed |
| guest_email | varchar(255) | Case-insensitive unique per host + open state |
| token | varchar(64) | Unique, random, hashed at rest |
| state | enum(`pending`,`approved`,`expired`,`revoked`) | Backed by Laravel state machine |
| expires_at | timestamp | Default `created_at + 72 hours` |
| approved_at | timestamp nullable | Set when host approves |
| revoked_at | timestamp nullable | Set when revoked |
| approved_by | bigint FK → users.id nullable | Usually same as host |
| metadata | jsonb | Stores guest notes, lobby request reference |
| created_at / updated_at | timestamps | |

**Indexes**: `idx_invitations_host_state` (`host_id`, `state`), `idx_invitations_expires_at` for expiry sweeps.

### access_logs

| Column | Type | Rules |
| --- | --- | --- |
| id | uuid (PK) | |
| actor_id | bigint FK → users.id nullable | Null for unauthenticated visitors |
| actor_role | enum(`public`,`guest`,`resident`) | Derived snapshot |
| target_type | enum(`area`,`room`) | |
| target_id | varchar(64) | `areas.slug` or `rooms.id` |
| outcome | enum(`allowed`,`denied`) | |
| message | text | Contextual denial/allow message |
| correlation_id | uuid | Propagated trace identifier |
| ip_address | inet | Stored for security auditing |
| user_agent | varchar(255) nullable | Device diagnostics |
| occurred_at | timestamp | Event time |
| created_at | timestamp | Inserted time |

**Retention**: Nightly job deletes rows older than 90 days (per clarification).

### lobby_invitation_requests

| Column | Type | Rules |
| --- | --- | --- |
| id | uuid (PK) | |
| requester_email | varchar(255) | Required, validated |
| message | text nullable | Optional request context |
| status | enum(`submitted`,`reviewed`,`converted`) | |
| reviewed_by | bigint FK → users.id nullable | |
| created_at / updated_at | timestamps | |

**Usage**: Stores public lobby requests to be triaged by residents; once approved, transforms into `invitations`.

## Relationships Diagram (textual)

- `User` 1↔N `Room`
- `Room` 1↔N `RoomPermission`
- `User (resident)` 1↔N `Invitation`
- `Invitation` 1→1 `RoomPermission` (parlour) upon approval (optional depending on share mode)
- `Invitation` 1↔N `AccessLog` (audits entry attempts)
- `LobbyInvitationRequest` 1→1 `Invitation` when converted

## State Machines

- **Invitation**: `pending` → `approved` (host action) → `expired` (scheduler) or `revoked` (host/admin). `approved` may transition to `revoked` for manual cancellation. State changes emit domain events for notifications and logging.
- **Room Share Mode**: `resident_only` ↔ `resident_and_guests` (subject to room type constraints).

## Validation Rules

- Emails validated via RFC, normalized lower-case.
- `share_mode` changes limited to parlour type.
- Guests cannot be granted sanctuary/den access; enforced in policies and data layer.
- Invitation tokens hashed using Laravel `Hash::make` to prevent plain-text leakage.

## Migration Ordering

1. Create `areas` seed (if not present) and `rooms` table.
2. Create `room_permissions` table.
3. Create `invitations` table with indices.
4. Create `access_logs` table.
5. Create `lobby_invitation_requests` table.
6. Backfill seed records (`areas`, default `rooms` per resident via seeder) once migrations applied.
