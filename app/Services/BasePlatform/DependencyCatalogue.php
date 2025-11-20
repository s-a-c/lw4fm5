<?php

declare(strict_types=1);

namespace App\Services\BasePlatform;

use App\Data\DependencyRecordData;
use App\Enums\DependencyClassification;
use App\Enums\ReviewCadence;
use App\Enums\RiskLevel;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;
use RuntimeException;
use ValueError;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */
final readonly class DependencyCatalogue
{
    private const string CATALOGUE_PATH = 'base-platform/dependencies.json';

    public function __construct(
        private Filesystem $filesystem,
    ) {}

    /**
     * @return Collection<int, DependencyRecordData>
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

        return collect($raw)->map(function (mixed $entry): DependencyRecordData {
            throw_unless(is_array($entry), InvalidArgumentException::class, 'Dependency catalogue entry must be an array');
            /** @var array<string, mixed> $entryTyped */
            $entryTyped = $entry;
            $this->assertValidEntry($entryTyped);

            $name = $entryTyped['name'] ?? '';
            $version = $entryTyped['version'] ?? '';
            $classification = $entryTyped['classification'] ?? '';
            $owner = $entryTyped['owner'] ?? '';
            $justification = $entryTyped['justification'] ?? '';
            $lastReviewedAtRaw = $entryTyped['lastReviewedAt'] ?? '';
            $reviewCadence = $entryTyped['reviewCadence'] ?? '';
            $riskLevel = $entryTyped['riskLevel'] ?? '';
            $notes = $entryTyped['notes'] ?? '';

            // After validation, enum values are guaranteed to be valid non-empty strings
            $classificationStr = is_string($classification) && $classification !== '' ? $classification : throw new InvalidArgumentException('Dependency entry classification must be a non-empty string');
            $reviewCadenceStr = is_string($reviewCadence) && $reviewCadence !== '' ? $reviewCadence : throw new InvalidArgumentException('Dependency entry reviewCadence must be a non-empty string');
            $riskLevelStr = is_string($riskLevel) && $riskLevel !== '' ? $riskLevel : throw new InvalidArgumentException('Dependency entry riskLevel must be a non-empty string');

            return new DependencyRecordData(
                name: is_string($name) ? $name : '',
                version: is_string($version) ? $version : '',
                classification: DependencyClassification::from($classificationStr),
                owner: is_string($owner) ? $owner : '',
                justification: is_string($justification) ? $justification : '',
                lastReviewedAt: Date::parse(is_string($lastReviewedAtRaw) ? $lastReviewedAtRaw : 'now')->startOfDay(),
                reviewCadence: ReviewCadence::from($reviewCadenceStr),
                riskLevel: RiskLevel::from($riskLevelStr),
                notes: is_string($notes) ? $notes : '',
            );
        });
    }

    /**
     * @return Collection<int, DependencyRecordData>
     */
    public function overdue(Carbon|CarbonImmutable|null $reference = null): Collection
    {
        $reference ??= Date::now();

        return $this->entries()->filter(fn (DependencyRecordData $record): bool => $record->isOverdue($reference));
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

        $classification = $entry['classification'] ?? '';
        $classificationStr = is_string($classification) ? $classification : '';
        if ($classificationStr === '') {
            throw new InvalidArgumentException('Dependency entry classification cannot be empty');
        }
        try {
            DependencyClassification::from($classificationStr);
        } catch (ValueError $e) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported dependency classification [%s]',
                $classificationStr
            ), 0, $e);
        }

        $reviewCadence = $entry['reviewCadence'] ?? '';
        $reviewCadenceStr = is_string($reviewCadence) ? $reviewCadence : '';
        if ($reviewCadenceStr === '') {
            throw new InvalidArgumentException('Dependency entry reviewCadence cannot be empty');
        }
        try {
            ReviewCadence::from($reviewCadenceStr);
        } catch (ValueError $e) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported review cadence [%s]',
                $reviewCadenceStr
            ), 0, $e);
        }

        $riskLevel = $entry['riskLevel'] ?? '';
        $riskLevelStr = is_string($riskLevel) ? $riskLevel : '';
        if ($riskLevelStr === '') {
            throw new InvalidArgumentException('Dependency entry riskLevel cannot be empty');
        }
        try {
            RiskLevel::from($riskLevelStr);
        } catch (ValueError $e) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported risk level [%s]',
                $riskLevelStr
            ), 0, $e);
        }
    }
}
