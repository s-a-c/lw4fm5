<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\BasePlatformMetrics;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

final class PolicyChecksumMonitor extends Command
{
    protected $signature = 'policy:checksum-monitor {--once : Run a single checksum validation pass}';

    protected $description = 'Verify policy acknowledgement headers for drift.';

    public function handle(): int
    {
        $expectedChecksum = Config::get('base-platform.policy.acknowledgement_checksum');
        $expected = is_string($expectedChecksum) ? $expectedChecksum : '';

        $filesConfig = Config::get('base-platform.policy.files', []);
        $files = Collection::make(is_array($filesConfig) ? $filesConfig : []);

        $missing = collect();
        $mismatched = collect();

        $files->each(function (mixed $relative) use ($expected, $missing, $mismatched): void {
            if (! is_string($relative)) {
                return;
            }
            $path = base_path($relative);

            if (! File::exists($path)) {
                $missing->push($relative);

                return;
            }

            $contents = File::get($path);

            $matches = [];
            if (preg_match('/Compliant with \[\.ai\/AI-GUIDELINES.md\]\(.+?\) v([a-f0-9]+)/i', $contents, $matches) !== 1) {
                $missing->push($relative);

                return;
            }

            $actual = $matches[1];

            if ($actual !== $expected) {
                $mismatched->push([
                    'file' => $relative,
                    'checksum' => $actual,
                ]);
            }
        });

        $this->components->info('Policy acknowledgement checksum summary');
        $this->line(sprintf('- Expected checksum: %s', $expected));
        $this->line(sprintf('- Files scanned: %d', $files->count()));

        if ($missing->isNotEmpty()) {
            $this->components->error('Missing or malformed acknowledgement headers detected:');
            $missing->each(function (mixed $file): void {
                if (is_string($file)) {
                    $this->line(" • {$file}");
                }
            });
        }

        if ($mismatched->isNotEmpty()) {
            $this->components->warn('Checksum drift detected:');
            $this->processMismatchedEntries($mismatched);
        }

        $status = $missing->isEmpty() && $mismatched->isEmpty() ? 'pass' : 'fail';

        BasePlatformMetrics::record('policy_checksum_status', [
            'status' => $status,
        ]);

        return $status === 'pass' ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Process mismatched entries and output checksum drift information.
     *
     * @param  Collection<int, array{file: string, checksum: string}|mixed>  $mismatched
     */
    private function processMismatchedEntries(Collection $mismatched): void
    {
        $mismatched->each(function (mixed $entry): void {
            // Guard against non-array values to satisfy static analysis and runtime safety
            if (! is_array($entry)) {
                return;
            }

            if (isset($entry['file'], $entry['checksum'])) {
                $file = is_string($entry['file']) ? $entry['file'] : '';
                $checksum = is_string($entry['checksum']) ? $entry['checksum'] : '';
                $this->line(sprintf(
                    ' • %s has checksum %s',
                    $file,
                    $checksum
                ));
            }
        });
    }
}
