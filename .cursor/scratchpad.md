# Scratchpad

---

Herd's CLI doesn't currently expose a `dumps` subcommand, so `herd dumps stop` fails even though the dump helper keeps intercepting Artisan output. The dump server is managed by the Herd desktop app.

**To disable dump interception:**

1. Open the Herd desktop app
2. Navigate to the **Dumps** tab/view
3. Click the **antenna icon** in the top toolbar (the icon with three radiating arcs)
   - This icon toggles dump interception on/off
   - When active, it intercepts `dump()` and `dd()` calls
   - Click it to deactivate dump interception

The antenna icon is the primary control for dump interception—there's no separate "Stop Dump Server" menu option. When the icon is inactive (not highlighted/pressed), dump interception is disabled.

**However**, disabling dump interception alone doesn't fix `php artisan filament:make-user` failing silently. The actual issues are:

1. **PHP 8.5 compatibility**: Monolog 3.9.0 (and even dev-main as of 2025-10-27) has a fatal error with PHP 8.5's stricter `PsrExt\Log\LoggerInterface` type checking. PHP 8.5 expects untyped parameters (`emergency($message, array $context = [])`), but Monolog uses typed signatures (`emergency(string|\Stringable $message, array $context = []): void`).
2. **Dump helper PHAR**: Even with interception disabled, Herd's dump helper PHAR is still loaded and causes `__wakeup()` deprecation errors when Whoops/Collision tries to render exceptions

**Workarounds:**

- **Use PHP 8.4**: Switch to PHP 8.4 instead of 8.5 (e.g., `herd use php@8.4` or use Homebrew PHP 8.4)
- **Wait for Monolog fix**: Monitor [Monolog GitHub](https://github.com/Seldaek/monolog) for PHP 8.5 compatibility updates
- **Use Herd's PHP**: `herd php artisan filament:make-user` may work if Herd bundles PHP 8.4 ([Herd Docs – macOS](https://herd.laravel.com/docs/macos/))

---

create `resources/views/pages/tailwindcss.catppuccin.blade.php` by converting @tailwindcss.catppuccin.html
the result should be a folio-routed, livewire- and flux-enabled component, fitting within the existing layout

---

Context: I’m working on a Laravel 12 app with Filament v5, Livewire v4, Pest v4, Tailwind v4. PHP 8.4. CI script is `composer ci:local` which runs: Pint + Rector + Prettier, Pest tests with coverage (gate set to exactly 100%), PHPStan, security audit, and a policy checksum monitor.

What’s already done:
- Rector config adjusted to skip Carbon→Date conversion where mutable Carbon is required.
- PHPStan error fixed in `PolicyChecksumMonitor` by guarding non-array entries.
- Added many targeted tests; lint and PHPStan are green. Overall coverage is currently ~88% (last run showed ~87.9%).
- We also discussed Composer process timeout and how to set `process-timeout` permanently in `composer.json` or globally.

Goal:
- Reach exactly 100% code coverage so `composer ci:local` passes end-to-end.

What I want next:
- Please review the current coverage report and propose the smallest set of additional tests to reach 100%.
- Prioritize files still below 100% (e.g., any remaining command/service branches, `DependencyCatalogue` edge lines, providers with partial lines). Suggest 1–3 concrete tests with short outlines, then implement them.
- If needed, temporarily increase Composer’s process timeout during local runs (e.g., `COMPOSER_PROCESS_TIMEOUT=1200 composer ci:local`) to prevent timeouts while iterating.

Constraints & preferences:
- Follow the project’s coding standards (Pint), Rector rules, and Boost/Laravel testing guidelines.
- Use Pest tests; keep them focused and fast.
- Don’t relax the coverage gate — we must truly hit 100%.

---
