<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\DependencyClassification;
use App\Enums\ReviewCadence;
use App\Enums\RiskLevel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;

final class DependencyRecordData extends Data
{
    public function __construct(
        public string $name,
        public string $version,
        public DependencyClassification $classification,
        public string $owner,
        public string $justification,
        public Carbon|CarbonImmutable $lastReviewedAt,
        public ReviewCadence $reviewCadence,
        public RiskLevel $riskLevel,
        public string $notes,
    ) {}

    public function reviewDeadline(): Carbon
    {
        $date = $this->lastReviewedAt instanceof Carbon
            ? $this->lastReviewedAt
            : Carbon::parse($this->lastReviewedAt->toDateTimeString());

        return match ($this->reviewCadence) {
            ReviewCadence::Monthly => $date->copy()->addMonthNoOverflow()->endOfDay(),
            ReviewCadence::Quarterly => $date->copy()->addMonthsNoOverflow(3)->endOfDay(),
        };
    }

    public function isOverdue(Carbon|CarbonImmutable $reference): bool
    {
        $carbonRef = $reference instanceof Carbon
            ? $reference
            : Date::parse($reference->toDateTimeString());

        return $this->reviewDeadline()->lessThanOrEqualTo($carbonRef);
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'classification' => $this->classification->value,
            'owner' => $this->owner,
            'justification' => $this->justification,
            'lastReviewedAt' => $this->lastReviewedAt->toDateString(),
            'reviewCadence' => $this->reviewCadence->value,
            'riskLevel' => $this->riskLevel->value,
            'notes' => $this->notes,
            'reviewDeadline' => $this->reviewDeadline()->toDateString(),
        ];
    }
}
