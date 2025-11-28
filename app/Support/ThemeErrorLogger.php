<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Structured error logging for theme-related errors (T027b1, FR-104).
 *
 * Ensures error logs include sufficient context for debugging (stack traces,
 * request context, user context) without exposing sensitive data.
 */
final class ThemeErrorLogger
{
    /**
     * Log an error with full context (T027b1, FR-104).
     *
     * @param  string  $message  Error message
     * @param  Throwable|null  $exception  Exception object (stack trace included automatically)
     * @param  array<string, mixed>  $additionalContext  Additional context data
     */
    public static function error(string $message, ?Throwable $exception = null, array $additionalContext = []): void
    {
        $context = self::buildContext($exception, $additionalContext);

        if ($exception instanceof Throwable) {
            Log::error($message, array_merge($context, ['exception' => $exception]));
        } else {
            Log::error($message, $context);
        }
    }

    /**
     * Log a warning with full context (T027b1, FR-104).
     *
     * @param  string  $message  Warning message
     * @param  Throwable|null  $exception  Exception object (stack trace included automatically)
     * @param  array<string, mixed>  $additionalContext  Additional context data
     */
    public static function warning(string $message, ?Throwable $exception = null, array $additionalContext = []): void
    {
        $context = self::buildContext($exception, $additionalContext);

        if ($exception instanceof Throwable) {
            Log::warning($message, array_merge($context, ['exception' => $exception]));
        } else {
            Log::warning($message, $context);
        }
    }

    /**
     * Build structured context for error logging (T027b1, FR-104).
     *
     * Includes:
     * - Request context (URL, method, IP, user agent) - no sensitive data
     * - User context (user ID only, no email/password)
     * - Session context (session ID)
     * - Application context (request ID, timestamp, timezone)
     * - Exception context (class, message, code, file, line) - when exception provided
     *
     * @param  Throwable|null  $exception  Exception object
     * @param  array<string, mixed>  $additionalContext  Additional context
     * @return array<string, mixed>
     */
    private static function buildContext(?Throwable $exception = null, array $additionalContext = []): array
    {
        $user = Auth::user();
        $request = request();

        $context = [
            // Application context
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'request_id' => $request->header('X-Request-ID') ?? uniqid('req_', true),

            // User context (no sensitive data)
            'user_id' => $user?->id,
            'user_authenticated' => $user !== null,

            // Session context
            'session_id' => session()->getId(),

            // Request context (no sensitive data like passwords, tokens)
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method(),
            'request_ip' => $request->ip(),
            'request_user_agent' => $request->userAgent(),
            'request_referer' => $request->header('Referer'),
        ];

        // Exception context (when exception provided)
        if ($exception instanceof Throwable) {
            $context['exception_class'] = $exception::class;
            $context['exception_message'] = $exception->getMessage();
            $context['exception_code'] = $exception->getCode();
            $context['exception_file'] = $exception->getFile();
            $context['exception_line'] = $exception->getLine();
            // Stack trace is automatically included by Laravel when exception is passed to Log
        }

        // Merge additional context (allows overriding defaults)
        return array_merge($context, $additionalContext);
    }
}
