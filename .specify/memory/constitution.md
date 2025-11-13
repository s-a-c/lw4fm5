Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
<!--
Sync Impact Report (2025-11-07)
- Version: 1.1.0 → 1.1.1
- Modified principles: None
- Added sections: None
- Removed sections: None
- Templates: ✅ .specify/templates/plan-template.md, ✅ .specify/templates/tasks-template.md
- Follow-up TODOs: None
-->

# lw4fm5 Constitution

<details>
<summary>Expand for Table of Contents</summary>

- [lw4fm5 Constitution](#lw4fm5-constitution)
  - [1. Core Principles](#1-core-principles)
    - [1.1. Policy-Driven Delivery](#11-policy-driven-delivery)
    - [1.2. Test-First Development (Non-Negotiable)](#12-test-first-development-non-negotiable)
    - [1.3. Clarity for Junior Developers](#13-clarity-for-junior-developers)
    - [1.4. Multi-Service Architecture with Clear Boundaries](#14-multi-service-architecture-with-clear-boundaries)
    - [1.5. Laravel-First Implementation \& Code Quality](#15-laravel-first-implementation--code-quality)
    - [1.6. Security, Observability, and Performance](#16-security-observability-and-performance)
  - [2. Operating Standards](#2-operating-standards)
  - [3. Additional Standards](#3-additional-standards)
    - [3.1. Database Conventions](#31-database-conventions)
    - [3.2. Exception Handling](#32-exception-handling)
    - [3.3. API Design](#33-api-design)
    - [3.4. Deployment](#34-deployment)
  - [4. Workflow \& Tooling](#4-workflow--tooling)
  - [5. Development Workflow](#5-development-workflow)
    - [5.1. Feature Development](#51-feature-development)
    - [5.2. Git Workflow](#52-git-workflow)
  - [6. Governance](#6-governance)

</details>

## 1. Core Principles

### 1.1. Policy-Driven Delivery

Every artifact must acknowledge and comply with the orchestration policy defined in `.ai/AI-GUIDELINES.md`. Before altering code or documentation, confirm the current checksum, cite applicable rules for sensitive changes, and follow the decision-making protocol (development standards, security, performance, testing, documentation).

### 1.2. Test-First Development (Non-Negotiable)

Practice TDD without exception:

1. Draft a test plan describing expected behavior.
2. Secure user approval for the plan.
3. Add failing tests that codify the behavior.
4. Run tests to confirm failure (Red).
5. Implement the minimal change to pass (Green).
6. Refactor safely while keeping tests green.
7. Obtain user approval prior to commit.
Maintain ≥90% global coverage and 100% on critical paths (authentication, payments, synchronization). Structure suites as `tests/Architecture` (layer rules, naming conventions using `pestphp/pest-plugin-arch`), `tests/Feature` (HTTP endpoints, queues, flows), and `tests/Unit` (pure logic). Submissions lacking approved tests are rejected at CI.

### 1.3. Clarity for Junior Developers

All outputs must be actionable for a developer with 6–24 months experience. Provide context, explain why choices are made, surface pitfalls, and set explicit success criteria. Favor structure, visual aids, and examples that accelerate onboarding.

### 1.4. Multi-Service Architecture with Clear Boundaries

Respect the domain split:

- **CRM Service**: Contacts, lifecycle management, history.
- **E-commerce Service**: Lunar catalog, carts, checkout, orders.
- **ERP Service**: Inventory source of truth, fulfillment, invoicing, suppliers.
- **Storefront**: Public headless consumer of Lunar APIs.
Each service owns its PostgreSQL database, exposes RESTful OpenAPI 3.0 contracts, and synchronizes asynchronously via Laravel queues. Cross-database access is forbidden.

### 1.5. Laravel-First Implementation & Code Quality

Follow Laravel 12 conventions: artisan generators, form requests, named routes, Volt/Livewire components where appropriate, and reuse existing patterns before inventing new ones. Apply mandatory quality gates—PHPStan level `max`, Laravel Pint formatting, Rector automation, Pest architecture tests, and a fully green CI pipeline before merge.

### 1.6. Security, Observability, and Performance

Security: encrypt PII with Laravel casts, enforce Sanctum tokens for service-to-service auth, Socialite OAuth for storefront users, MFA-capable auth for admins, and FilamentShield RBAC (Administrator, Editor, Product Manager, Order Manager with granular CRUD permissions). Validate all inputs with form requests, enable CSRF for web routes, sanitize output, and rate-limit authentication endpoints.
Observability: emit structured JSON logs (INFO default in production), propagate W3C Trace Context correlation IDs across HTTP and queues using OpenTelemetry, and expose Prometheus metrics (RED for APIs, USE for resources, queue depth/throughput).
Performance: meet P95 <500ms service APIs, <1s storefront interactions, support 1,000 concurrent users, and sustain 1,000 queued jobs/minute. Keep services stateless, scale horizontally, use Redis for cache/queues, apply eager loading, index frequently queried fields, cache heavy queries, and offload non-critical work asynchronously.

## 2. Operating Standards

- **Guideline Review**: Before implementation, consult `AGENTS.md` and relevant `.ai/AI-GUIDELINES/` documents (development, security, performance, testing, documentation).
- **Tool Usage**: Leverage Laravel Boost tooling—`search-docs` for authoritative references, `tinker`/`database-query` for inspection, browser automation for UI validation, and `todo_write` for task tracking.
- **Documentation Practices**: Author docs only when requested. Ensure accessibility, include mermaid diagrams when visuals add clarity, and prepend the policy acknowledgement header.
- **Testing Strategy**: Follow the TDD mandate, categorize tests with `#[Group]`, use datasets for validation coverage, and run the narrowest suite necessary while maintaining ≥90% coverage.
- **Performance Mindset**: Prevent N+1 queries with eager loading, use caching where appropriate, monitor memory usage, and schedule long-running work via queues with Horizon oversight.

## 3. Additional Standards

### 3.1. Database Conventions

- One migration per logical change with descriptive timestamps, transactional safety, and reversible `down()` methods.
- Type-hint model properties, define casts via `casts()` (including encrypted casts for PII), enable soft deletes, and consider `spatie/laravel-data` for DTOs.
- Prefer `spatie/laravel-query-builder` for API-level filtering/sorting, eager load relationships, and introduce repositories only when multiple data sources or complex orchestration justifies them.

### 3.2. Exception Handling

- Implement domain-specific exceptions (e.g., `CustomerNotFoundException extends DomainException`) and render consistent JSON responses that include the propagated `X-Correlation-ID` in `app/Exceptions/Handler.php`.

### 3.3. API Design

- Expose RESTful, versioned endpoints under `/api/v1/` with resource-oriented URLs, JSON payloads, pagination (default 50 items), and filtering via `spatie/laravel-query-builder` (e.g., `?filter[email]=example.com&sort=-created_at`).

### 3.4. Deployment

- Containerize every service, publish to GHCR with semantic version and commit SHA tags, execute CI/CD via GitHub Actions, deploy staging automatically on `main`, and require manual approval for production.

## 4. Workflow & Tooling

- **Task Lifecycle**: Break work into explicit to-dos, keep one active, and update statuses as progress occurs. Apply red-green-refactor during implementation.
- **Change Process**: Use artisan generators for Laravel components, maintain PSR-12 imports, enforce strict types, and reuse existing components before expanding the structure or dependencies.
- **Reviews & Approvals**: Flag risks, regressions, security gaps, and missing tests in reviews before summarizing outcomes. Challenge assumptions and seek clarification on ambiguities.
- **Knowledge Management**: Query `byterover-retrieve-knowledge` before exploring unfamiliar areas; log reusable patterns with `byterover-store-knowledge` when access is available.
- **Communication Style**: Stay direct, professional, and dry-humored; provide recommendations with confidence scores and alternatives when appropriate.

## 5. Development Workflow

### 5.1. Feature Development

1. Draft or update the feature spec in `specs/###-feature/spec.md`.
2. Use `/speckit.clarify` for ambiguity resolution, `/speckit.plan` for implementation planning, and `/speckit.tasks` for hierarchical task creation.
3. Execute the TDD cycle for each task (test plan → approval → failing tests → red → green → refactor → approval) before committing.

### 5.2. Git Workflow

- Branch naming: `###-feature-name` (e.g., `001-integrated-commerce-platform`).
- Commits follow Conventional Commit prefixes (`feat:`, `fix:`, `test:`, `docs:`).
- Require green CI, successful architecture tests, reviewer approval, and user acceptance prior to squash-merging into `main`.

## 6. Governance

- This constitution supersedes conflicting practices across the workspace and external advisors; resolve conflicts in favor of this document.
- Any drift in `AI-GUIDELINES` checksums demands immediate acknowledgement updates and re-validation before additional work.
- Sensitive actions (security, CI, external access) require explicit rule citations referencing the relevant guideline section.
- Pull requests and reviews must verify adherence, including acknowledgement headers, guideline citations, quality gate evidence, and test artifacts.
- Violations—missing tests, failing quality gates, security or performance regressions—must be surfaced immediately; unresolved issues block approvals until remediated.
- Amendments require documented rationale, stakeholder approval, and an actionable migration plan for existing code.

**Version**: 1.1.1 | **Ratified**: 2025-11-07 | **Last Amended**: 2025-11-07
