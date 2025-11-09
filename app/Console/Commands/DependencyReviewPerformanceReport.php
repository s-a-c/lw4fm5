<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\BasePlatformMetrics;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use JsonException;
use RuntimeException;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */
final class DependencyReviewPerformanceReport extends Command
{
    protected $signature = 'platform:dependency-review-performance-report
        {--report= : Relative storage path to a dependency review report}';

    protected $description = 'Summarise dependency review runtime metrics and append evidence logs for QA.';

    private const REPORT_DIRECTORY = 'base-platform/dependency-reports';

    private const PERFORMANCE_LOG = 'base-platform/dependency-performance.log';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $reportPath = $this->option('report');

        if (! is_string($reportPath) || $reportPath === '') {
            try {
                $reportPath = $this->resolveLatestReport();
            } catch (RuntimeException $exception) {
                $this->components->error($exception->getMessage());

                return self::FAILURE;
            }
        }

        if (! $disk->exists($reportPath)) {
            $this->components->error(sprintf('Dependency review report [%s] could not be found.', $reportPath));

            return self::FAILURE;
        }

        try {
            $report = json_decode(
                json: $disk->get($reportPath),
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            $this->components->error(sprintf('Dependency review report [%s] contains invalid JSON: %s', $reportPath, $exception->getMessage()));

            return self::FAILURE;
        }

        if (! is_array($report)) {
            $this->components->error(sprintf('Dependency review report [%s] must decode to an array.', $reportPath));

            return self::FAILURE;
        }

        $entry = [
            'recorded_at' => Carbon::now()->toIso8601String(),
            'report_path' => $reportPath,
            'status' => $report['status'] ?? 'unknown',
            'runtime_seconds' => $report['runtime_seconds'] ?? null,
            'overdue_count' => is_array($report['overdue_reviews'] ?? null) ? count($report['overdue_reviews']) : 0,
            'severity_counts' => $report['severity_counts'] ?? [],
            'issue_template' => $report['issue_template'] ?? null,
        ];

        $disk->append(
            self::PERFORMANCE_LOG,
            json_encode($entry, JSON_THROW_ON_ERROR)
        );

        $this->components->info(sprintf(
            'Dependency review performance entry recorded for %s.',
            $reportPath
        ));

        BasePlatformMetrics::record('dependency_review_performance', [
            'status' => $entry['status'],
            'overdue_count' => $entry['overdue_count'],
        ], (float) ($entry['runtime_seconds'] ?? 0.0));

        return self::SUCCESS;
    }

    private function resolveLatestReport(): string
    {
        $disk = Storage::disk('local');

        $files = collect($disk->files(self::REPORT_DIRECTORY))
            ->filter(fn (string $path) => Str::endsWith($path, '.json'))
            ->sortDesc()
            ->values();

        if ($files->isEmpty()) {
            throw new RuntimeException('No dependency review reports are present in storage.');
        }

        return $files->first();
    }
}
