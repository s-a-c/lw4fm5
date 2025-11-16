<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\BasePlatform\ComposerAuditRunnerContract;
use App\Services\BasePlatform\DependencyCatalogue;
use App\Services\BasePlatform\DependencyRecord;
use App\Support\BasePlatformMetrics;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */
final class DependencyReviewReport extends Command
{
    private const string DEFAULT_REPORT_DIRECTORY = 'base-platform/dependency-reports';

    protected $signature = 'platform:dependency-review
        {--output= : Relative storage path for the generated report}
        {--issue-template=.github/ISSUE_TEMPLATE/dependency-review.md : GitHub issue template used by automation}';

    protected $description = 'Generate the monthly dependency review report and emit governance metrics.';

    public function __construct(
        private readonly ComposerAuditRunnerContract $auditRunner,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $startedAt = microtime(true);
        $nowImmutable = Date::now();
        // Note: Using Carbon::parse() instead of Date::parse() because DependencyCatalogue::overdue() requires Carbon (mutable), not CarbonImmutable
        $now = Carbon::parse($nowImmutable->toDateTimeString());

        $disk = Storage::disk('local');
        $catalogue = new DependencyCatalogue($disk);
        $dependencies = $catalogue->entries();
        $overdue = $catalogue->overdue($now);

        $severityCounts = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
        ];

        $auditStatus = 'pass';
        $auditError = null;

        $result = $this->auditRunner->run();

        if (! $result->successful()) {
            $auditStatus = 'fail';
            $auditError = mb_trim($result->errorOutput()) !== '' ? mb_trim($result->errorOutput()) : $result->output();
        } else {
            $decoded = json_decode($result->output(), true);

            if (! is_array($decoded)) {
                $auditStatus = 'fail';
                $auditError = 'Composer audit returned malformed JSON output.';
            } else {
                $advisoriesRaw = Arr::get($decoded, 'advisories', []);
                $advisories = is_array($advisoriesRaw) ? $advisoriesRaw : [];
                /** @var array<int, array<string, mixed>> $advisoriesTyped */
                $advisoriesTyped = array_filter($advisories, is_array(...));
                $severityCounts = $this->tallySeverities($advisoriesTyped);

                if ($severityCounts['critical'] > 0 || $severityCounts['high'] > 0) {
                    $auditStatus = 'fail';
                } elseif ($severityCounts['medium'] > 0 || $overdue->isNotEmpty()) {
                    $auditStatus = 'warn';
                }
            }
        }

        $reportPath = $this->determineOutputPath($now);

        $disk->makeDirectory(self::DEFAULT_REPORT_DIRECTORY);

        $report = [
            'generated_at' => $now->toIso8601String(),
            'status' => $auditStatus,
            'runtime_seconds' => round(microtime(true) - $startedAt, 3),
            'severity_counts' => $severityCounts,
            'overdue_reviews' => $overdue->map->toArray()->values()->all(),
            'dependencies' => $dependencies->map->toArray()->values()->all(),
            'issue_template' => $this->option('issue-template'),
            'composer_command' => 'composer audit --format=json',
            'error' => $auditError,
        ];

        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        $disk->put($reportPath, $json);

        File::ensureDirectoryExists(storage_path('app/'.dirname($reportPath)));
        File::put(storage_path('app/'.$reportPath), $json);

        if (app()->runningUnitTests()) {
            $testingRoot = storage_path('framework/testing/disks/local');

            File::ensureDirectoryExists($testingRoot.'/'.dirname($reportPath));
            File::put($testingRoot.'/'.$reportPath, $json);
        }

        $this->components->info(sprintf(
            'Dependency review report generated: storage/app/%s',
            $reportPath
        ));

        if ($overdue->isNotEmpty()) {
            $this->components->warn(sprintf(
                'Overdue dependencies detected: %s',
                $overdue->map(fn (DependencyRecord $entry): string => $entry->name)->implode(', ')
            ));
        }

        if ($auditError !== null) {
            $this->components->error($auditError);
        }

        BasePlatformMetrics::record('dependency_review_status', [
            'status' => $auditStatus,
            'overdue_count' => $overdue->count(),
            'critical' => $severityCounts['critical'],
            'high' => $severityCounts['high'],
            'medium' => $severityCounts['medium'],
            'low' => $severityCounts['low'],
        ]);

        BasePlatformMetrics::record('dependency_review_runtime_seconds', [
            'status' => $auditStatus,
        ], $report['runtime_seconds']);

        return $auditError === null ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @param  array<int, array<string, mixed>>  $advisories
     * @return array<string, int>
     */
    private function tallySeverities(array $advisories): array
    {
        $counts = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
        ];

        Collection::make($advisories)
            ->each(function (array $advisory) use (&$counts): void {
                $severityValue = $advisory['severity'] ?? 'low';
                $severity = mb_strtolower(is_string($severityValue) ? $severityValue : 'low');

                if (! array_key_exists($severity, $counts)) {
                    $severity = 'low';
                }

                $counts[$severity]++;
            });

        return $counts;
    }

    private function determineOutputPath(Carbon $now): string
    {
        $option = $this->option('output');

        if (is_string($option) && $option !== '') {
            return mb_ltrim($option, '/');
        }

        return sprintf(
            '%s/%s-dependency-review.json',
            self::DEFAULT_REPORT_DIRECTORY,
            $now->format('Y-m')
        );
    }
}
