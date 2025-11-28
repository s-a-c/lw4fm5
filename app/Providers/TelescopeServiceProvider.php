<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

final class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(fn (IncomingEntry $entry): bool => $this->shouldRecordEntry($entry, $isLocal));

        // Tag theme events for filtering (T027a, FR-036)
        Telescope::tag(function (IncomingEntry $entry): array {
            $tags = [];

            // Check if this is a log entry with theme event type
            if ($entry->type === 'log') {
                $content = $entry->content;
                $message = $content['message'] ?? '';
                $context = $content['context'] ?? [];
                $eventType = $context['event_type'] ?? null;

                if (in_array($eventType, ['theme_changed', 'validation_corrected', 'preview_interaction'], true)) {
                    $tags[] = 'theme:event';
                    $tags[] = 'theme:'.$eventType;
                }
            }

            return $tags;
        });
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', fn (?User $user): bool => false);
    }

    /**
     * Determine if an entry should be recorded by Telescope.
     */
    private function shouldRecordEntry(IncomingEntry $entry, bool $isLocal): bool
    {
        if ($isLocal) {
            return true;
        }
        if ($entry->isReportableException()) {
            return true;
        }
        if ($entry->isFailedRequest()) {
            return true;
        }
        if ($entry->isFailedJob()) {
            return true;
        }
        if ($entry->isScheduledTask()) {
            return true;
        }

        // Record theme events even in non-local environments (T027a, FR-036)
        if ($entry->hasMonitoredTag()) {
            return true;
        }

        // Check if this is a theme event (tagged with theme:event)
        if ($entry->type === 'log') {
            $content = $entry->content;
            $context = $content['context'] ?? [];
            $eventType = $context['event_type'] ?? null;

            if (in_array($eventType, ['theme_changed', 'validation_corrected', 'preview_interaction'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    private function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }
}
