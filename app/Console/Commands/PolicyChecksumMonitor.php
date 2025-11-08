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
        $expected = (string) Config::get('base-platform.policy.acknowledgement_checksum');
        $files = Collection::make(Config::get('base-platform.policy.files', []));

        $missing = collect();
        $mismatched = collect();

        $files->each(function (string $relative) use ($expected, $missing, $mismatched): void {
            $path = base_path($relative);

            if (! File::exists($path)) {
                $missing->push($relative);

                return;
            }

            $contents = File::get($path);

            if (! preg_match('/Compliant with \[\.ai\/AI-GUIDELINES.md\]\(.+?\) v([a-f0-9]+)/i', $contents, $matches)) {
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
            $missing->each(fn (string $file) => $this->line(" • {$file}"));
        }

        if ($mismatched->isNotEmpty()) {
            $this->components->warn('Checksum drift detected:');
            $mismatched->each(fn (array $entry) => $this->line(sprintf(
                ' • %s has checksum %s',
                $entry['file'],
                $entry['checksum']
            )));
        }

        $status = $missing->isEmpty() && $mismatched->isEmpty() ? 'pass' : 'fail';

        BasePlatformMetrics::record('policy_checksum_status', [
            'status' => $status,
        ]);

        return $status === 'pass' ? self::SUCCESS : self::FAILURE;
    }
}
