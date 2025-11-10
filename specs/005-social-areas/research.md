# Research Dossier: Social Areas Provisioning Phase 1

Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Decision 1: Access Control Architecture

- **Decision**: Implement Laravel authorization policies scoped to `Area`, `Room`, and `Invitation` models, backed by Sanctum-authenticated requests and role claims persisted on the `users` table.
- **Rationale**: Policies integrate with Laravel middleware, simplify enforcement per FR-001/FR-003, and align with constitution requirements for form request validation and Sanctum tokens without introducing new dependencies.
- **Alternatives Considered**: `spatie/laravel-permission` (adds migration overhead and role caching beyond Phase 1 needs); custom gate middleware (harder to test and risks inconsistent denial messaging).

## Decision 2: Audit Logging Persistence & Retention

- **Decision**: Store access attempts in a dedicated `access_logs` relational table with nightly queue-driven job to purge records older than 90 days while exporting metrics to structured logs.
- **Rationale**: Meets FR-006 retention requirements, leverages existing PostgreSQL infrastructure, and enables RED/USE observability signals via scheduled jobs without external systems.
- **Alternatives Considered**: External log pipeline (overkill for Phase 1 and increases integration risk); file-based logs (difficult to query and violates structured logging standards).

## Decision 3: Invitation Workflow & Notifications

- **Decision**: Represent invitations with a state machine (`pending`, `accepted`, `expired`, `revoked`), enforce 72-hour expiry via queue worker, and deliver notifications through the existing transactional mail provider plus in-app alerts.
- **Rationale**: Aligns with clarifications on concurrent invitations, expiry handling, and FR-008; state machine simplifies enforcement and testing scenarios for invitations and access approvals.
- **Alternatives Considered**: One-off cron scripts without state modeling (risk of inconsistent host notifications); SMS or third-party messaging (out-of-scope and increases cost/complexity).
