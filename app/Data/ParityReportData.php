<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ParityStatus;
use App\Models\EnvironmentProfile;
use App\Models\ParityResult;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ParityReportData extends Data
{
    /**
     * @param  list<string>  $issues
     */
    public function __construct(
        public string $profile,
        public ParityStatus $status,
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
            'status' => $this->status->value,
            'issues' => $this->issues,
        ]);

        $result->setRelation('environmentProfile', $environmentProfile);

        return $result;
    }
}
