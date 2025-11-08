<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use Illuminate\Support\Collection;

final class ParityReport
{
    public const STATUS_PASS = 'pass';

    public const STATUS_WARNING = 'warning';

    public const STATUS_FAIL = 'fail';

    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public readonly string $profile,
        public readonly string $status,
        public readonly array $issues = [],
    ) {}

    /**
     * @param  iterable<self>  $reports
     * @return Collection<int, ParityResult>
     */
    public static function persistMany(iterable $reports): Collection
    {
        return collect($reports)->map(function (self $report): ParityResult {
            $profile = EnvironmentProfile::query()->where('name', $report->profile)->firstOrFail();

            return $report->toParityResult($profile);
        });
    }

    public function toParityResult(EnvironmentProfile $environmentProfile): ParityResult
    {
        /** @var ParityResult $result */
        $result = ParityResult::query()->create([
            'environment_profile_id' => $environmentProfile->id,
            'run_date' => now(),
            'status' => $this->status,
            'issues' => $this->issues,
        ]);

        return $result;
    }
}
