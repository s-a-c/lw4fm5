<?php

declare(strict_types=1);

namespace App\Services\Theme;

use App\Data\UserSettingsData;
use App\Enums\Theme;
use App\Enums\ThemeAccent;
use App\Enums\ThemeFlavor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Security audit logging for theme operations (T027l, FR-077).
 *
 * Logs:
 * - Failed validations
 * - Unauthorized access attempts
 * - Rate limit violations
 * - Theme preference changes (user id, timestamp, previous value, new value, source IP)
 */
final class ThemeSecurityAuditLogger
{
    /**
     * Log theme preference change (T027l, FR-077).
     *
     * @param  UserSettingsData|null  $oldSettings  Previous theme settings
     * @param  UserSettingsData  $newSettings  New theme settings
     */
    public static function logThemeChange(?UserSettingsData $oldSettings, UserSettingsData $newSettings): void
    {
        $user = Auth::user();

        Log::info('Theme preference changed', [
            'event_type' => 'security_audit',
            'audit_type' => 'theme_preference_change',
            'user_id' => $user?->id,
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'previous_theme' => $oldSettings?->theme->value,
            'previous_flavor' => $oldSettings?->flavor->value,
            'previous_accent' => $oldSettings?->accent->value,
            'new_theme' => $newSettings->theme->value,
            'new_flavor' => $newSettings->flavor->value,
            'new_accent' => $newSettings->accent->value,
            'source_ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_id' => Request::header('X-Request-ID'),
        ]);
    }

    /**
     * Log failed validation attempt (T027l, FR-077).
     *
     * @param  Theme|null  $invalidTheme  Invalid theme value (if any)
     * @param  ThemeFlavor|null  $invalidFlavor  Invalid flavor value (if any)
     * @param  ThemeAccent|null  $invalidAccent  Invalid accent value (if any)
     * @param  string  $reason  Reason for validation failure
     */
    public static function logFailedValidation(
        ?Theme $invalidTheme,
        ?ThemeFlavor $invalidFlavor,
        ?ThemeAccent $invalidAccent,
        string $reason,
    ): void {
        $user = Auth::user();

        Log::warning('Theme validation failed', [
            'event_type' => 'security_audit',
            'audit_type' => 'validation_failure',
            'user_id' => $user?->id,
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'invalid_theme' => $invalidTheme?->value,
            'invalid_flavor' => $invalidFlavor?->value,
            'invalid_accent' => $invalidAccent?->value,
            'reason' => $reason,
            'source_ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_id' => Request::header('X-Request-ID'),
        ]);
    }

    /**
     * Log unauthorized access attempt (T027l, FR-077).
     *
     * @param  string  $resource  Resource that was accessed
     * @param  string  $reason  Reason for unauthorized access
     */
    public static function logUnauthorizedAccess(string $resource, string $reason): void
    {
        $user = Auth::user();

        Log::warning('Unauthorized theme access attempt', [
            'event_type' => 'security_audit',
            'audit_type' => 'unauthorized_access',
            'user_id' => $user?->id,
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'resource' => $resource,
            'reason' => $reason,
            'source_ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_id' => Request::header('X-Request-ID'),
        ]);
    }

    /**
     * Log rate limit violation (T027l, FR-077).
     *
     * @param  string  $key  Rate limit key
     * @param  int  $limit  Rate limit threshold
     * @param  int  $attempts  Number of attempts
     */
    public static function logRateLimitViolation(string $key, int $limit, int $attempts): void
    {
        $user = Auth::user();

        Log::warning('Theme rate limit violated', [
            'event_type' => 'security_audit',
            'audit_type' => 'rate_limit_violation',
            'user_id' => $user?->id,
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'rate_limit_key' => $key,
            'limit' => $limit,
            'attempts' => $attempts,
            'source_ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_id' => Request::header('X-Request-ID'),
        ]);
    }
}
