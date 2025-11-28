<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate limiting middleware for theme auto-save endpoint (T014, FR-020).
 *
 * Applies sliding window rate limiting (10 requests per 60 seconds per user)
 * to Livewire requests for the appearance component's performSave method.
 */
final class RateLimitThemeAutoSave
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply rate limiting to Livewire update requests
        if (! $this->isLivewireUpdateRequest($request)) {
            return $next($request);
        }

        // Check if this is a request for the appearance component's save method
        if (! $this->isAppearanceSaveRequest($request)) {
            return $next($request);
        }

        // Apply rate limiting (10 requests per 60 seconds per user)
        $key = $this->getRateLimitKey($request);

        if (RateLimiter::tooManyAttempts('theme-auto-save:'.$key, 10)) {
            $seconds = RateLimiter::availableIn('theme-auto-save:'.$key);

            return response()->json([
                'message' => __('Too many theme update attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]),
            ], 429)->withHeaders([
                'Retry-After' => $seconds,
                'X-RateLimit-Limit' => '10',
                'X-RateLimit-Remaining' => '0',
            ]);
        }

        RateLimiter::hit('theme-auto-save:'.$key, 60);

        $response = $next($request);

        // Add rate limit headers to response
        $remaining = RateLimiter::remaining('theme-auto-save:'.$key, 10);

        return $response->withHeaders([
            'X-RateLimit-Limit' => '10',
            'X-RateLimit-Remaining' => (string) $remaining,
        ]);
    }

    /**
     * Check if the request is a Livewire update request.
     */
    private function isLivewireUpdateRequest(Request $request): bool
    {
        if ($request->hasHeader('X-Livewire')) {
            return true;
        }
        if ($request->routeIs('livewire.update')) {
            return true;
        }

        return str_contains($request->path(), 'livewire/update');
    }

    /**
     * Check if this is a request for the appearance component's save method.
     */
    private function isAppearanceSaveRequest(Request $request): bool
    {
        // Check for component name in request
        $components = $request->input('components', []);

        foreach ($components as $component) {
            $name = $component['name'] ?? null;
            $updates = $component['updates'] ?? [];

            // Check if this is the appearance component
            if ($name === 'settings.appearance' || str_contains($name ?? '', 'settings.appearance')) {
                // Check if any update is calling performSave or retrySave
                foreach ($updates as $update) {
                    $method = $update['method'] ?? null;
                    if (in_array($method, ['performSave', 'retrySave'], true)) {
                        return true;
                    }
                }
            }
        }

        // Also check for direct method calls in Livewire requests
        $method = $request->input('method');
        $componentName = $request->input('componentName') ?? $request->input('component');
        if (! $componentName) {
            return false;
        }
        if (! str_contains((string) $componentName, 'settings.appearance')) {
            return false;
        }

        return in_array($method, ['performSave', 'retrySave'], true);
    }

    /**
     * Get the rate limit key for the request (user ID or IP).
     */
    private function getRateLimitKey(Request $request): string
    {
        $user = $request->user();

        return $user?->id ?? $request->ip();
    }
}
