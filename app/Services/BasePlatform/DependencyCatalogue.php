<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;
use RuntimeException;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */
final readonly class DependencyCatalogue
{
    private const string CATALOGUE_PATH = 'base-platform/dependencies.json';

    /**
     * @var list<string>
     */
    private const array VALID_CLASSIFICATIONS = ['core', 'optional', 'experimental'];

    /**
     * @var list<string>
     */
    private const array VALID_REVIEW_CADENCES = ['monthly', 'quarterly'];

    /**
     * @var list<string>
     */
    private const array VALID_RISK_LEVELS = ['high', 'medium', 'low'];

    public function __construct(
        private Filesystem $filesystem,
    ) {}

    /**
     * @return Collection<int, DependencyRecord>
     */
    public function entries(): Collection
    {
        if (! $this->filesystem->exists(self::CATALOGUE_PATH)) {
            throw new RuntimeException(sprintf('Dependency catalogue [%s] is missing', self::CATALOGUE_PATH));
        }

        $raw = json_decode(
            json: (string) $this->filesystem->get(self::CATALOGUE_PATH),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );

        throw_unless(is_array($raw), InvalidArgumentException::class, 'Dependency catalogue must decode to an array');

        return collect($raw)->map(function (array $entry): DependencyRecord {
            $this->assertValidEntry($entry);

            return new DependencyRecord(
                name: (string) $entry['name'],
                version: (string) $entry['version'],
                classification: (string) $entry['classification'],
                owner: (string) $entry['owner'],
                justification: (string) $entry['justification'],
                lastReviewedAt: Date::parse($entry['lastReviewedAt'])->startOfDay(),
                reviewCadence: (string) $entry['reviewCadence'],
                riskLevel: (string) $entry['riskLevel'],
                notes: (string) $entry['notes'],
            );
        });
    }

    /**
     * @return Collection<int, DependencyRecord>
     */
    public function overdue(?Carbon $reference = null): Collection
    {
        $reference ??= Date::now();

        return $this->entries()->filter(fn (DependencyRecord $record): bool => $record->isOverdue($reference));
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function assertValidEntry(array $entry): void
    {
        $required = [
            'name',
            'version',
            'classification',
            'owner',
            'justification',
            'lastReviewedAt',
            'reviewCadence',
            'riskLevel',
            'notes',
        ];

        foreach ($required as $key) {
            if (! array_key_exists($key, $entry)) {
                throw new InvalidArgumentException(sprintf('Dependency entry missing required key [%s]', $key));
            }
        }

        if (! in_array($entry['classification'], self::VALID_CLASSIFICATIONS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported dependency classification [%s]',
                $entry['classification']
            ));
        }

        if (! in_array($entry['reviewCadence'], self::VALID_REVIEW_CADENCES, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported review cadence [%s]',
                $entry['reviewCadence']
            ));
        }

        if (! in_array($entry['riskLevel'], self::VALID_RISK_LEVELS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported risk level [%s]',
                $entry['riskLevel']
            ));
        }
    }
}

/**
 * @psalm-type DependencyRecordArray = array{
 *     name: string,
 *     version: string,
 *     classification: string,
 *     owner: string,
 *     justification: string,
 *     lastReviewedAt: string,
 *     reviewCadence: string,
 *     riskLevel: string,
 *     notes: string,
 *     reviewDeadline: string,
 * }
 */
final readonly class DependencyRecord
{
    public function __construct(
        public string $name,
        public string $version,
        public string $classification,
        public string $owner,
        public string $justification,
        public Carbon $lastReviewedAt,
        public string $reviewCadence,
        public string $riskLevel,
        public string $notes,
    ) {}

    public function reviewDeadline(): Carbon
    {
        return match ($this->reviewCadence) {
            'monthly' => $this->lastReviewedAt->copy()->addMonthNoOverflow()->endOfDay(),
            'quarterly' => $this->lastReviewedAt->copy()->addMonthsNoOverflow(3)->endOfDay(),
            default => throw new InvalidArgumentException(sprintf(
                'Unsupported review cadence [%s]',
                $this->reviewCadence
            )),
        };
    }

    public function isOverdue(Carbon $reference): bool
    {
        return $this->reviewDeadline()->lessThanOrEqualTo($reference);
    }

    /**
     * @return DependencyRecordArray
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'classification' => $this->classification,
            'owner' => $this->owner,
            'justification' => $this->justification,
            'lastReviewedAt' => $this->lastReviewedAt->toDateString(),
            'reviewCadence' => $this->reviewCadence,
            'riskLevel' => $this->riskLevel,
            'notes' => $this->notes,
            'reviewDeadline' => $this->reviewDeadline()->toDateString(),
        ];
    }
}
