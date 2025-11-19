<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use Illuminate\Support\Collection;

final readonly class ParityReport
{
    public const string STATUS_PASS = 'pass';

    public const string STATUS_WARNING = 'warning';

    public const string STATUS_FAIL = 'fail';

    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public string $profile,
        public string $status,
        public array $issues = [],
    ) {}

    /**
     * @param  iterable<self>  $reports
     * @return Collection<int, ParityResult>
     */
    public static function persistMany(iterable $reports): Collection
    {
        $results = [];
        foreach ($reports as $report) {
            $profile = EnvironmentProfile::query()->where('name', $report->profile)->firstOrFail();
            $results[] = $report->toParityResult($profile);
        }

        return collect($results);
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

        $result->setRelation('environmentProfile', $environmentProfile);

        return $result;
    }
}
