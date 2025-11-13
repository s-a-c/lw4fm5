# Quickstart: Social Areas Provisioning Phase 1

Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e

## Prerequisites

- PHP 8.4.12 with extensions from `composer.json`.
- Node/Bun toolchain for asset rebuild (`npm` or `bun` as per repo instructions).
- Redis for queues/cache; Horizon configured.
- PostgreSQL with Storefront schema access.

## Setup Steps

1. **Sync mainline & install dependencies**

   ```bash
   composer install
   bun install
   ```

2. **Publish feature migrations & seeders** (artisans to be added in implementation):

   ```bash
   php artisan migrate
   php artisan db:seed --class=SocialAreasSeeder
   ```

3. **Queue & Scheduler**
   - Start Horizon locally: `php artisan horizon`.
   - Ensure `queue:work` processes `invitations` and `audits` queues.
   - Add cron entry (or `php artisan schedule:work`) for 90-day purge and hourly invitation expiry job.
4. **Environment Variables**
   - Confirm transactional mail provider credentials remain valid (`MAIL_MAILER`, `MAIL_FROM_ADDRESS`).
   - Configure `SOCIAL_AREAS_EXPIRY_MINUTES=4320` (72 hours) for flexibility.
5. **Seed Demo Data**

   ```bash
   php artisan social-areas:seed-demo --resident-email=resident@example.com
   ```

   (Command will create sample rooms, invitations, and lobby content for testing.)
6. **Run Tests**

   ```bash
   php artisan test --group=social-areas
   pest tests/Feature/SocialAreas
   ```

7. **Build Frontend (if UI adjustments land in Phase 1)**
   ```bash
   bun run build
   ```

## Verification Checklist

- Residents can toggle parlour sharing and see updates reflected in API/UI.
- Greenroom guest flow honors approvals and writes to `access_logs`.
- Lobby invitation request throttling prevents spam (429 on high frequency).
- Audit purge job logs deletions and retains only ≤90 days of entries.
- Emails appear in mail trap/preview via queued notifications.
