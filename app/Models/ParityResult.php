<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 *
 * @property string|null $environment_profile_id
 * @property ParityStatus|null $status
 * @property Carbon|null $run_date
 * @property array<int, string>|null $issues
 */
use App\Enums\ParityStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

final class ParityResult extends Model
{
    /** @use HasFactory<never> */
    use HasFactory;

    use HasUuids;

    public $incrementing = false;

    public ?ParityStatus $status = null;

    public ?Carbon $run_date = null;

    /**
     * @var array<int, string>|null
     */
    public ?array $issues = null;

    protected $fillable = [
        'environment_profile_id',
        'run_date',
        'status',
        'issues',
    ];

    protected $keyType = 'string';

    /**
     * @return BelongsTo<EnvironmentProfile, $this>
     */
    public function environmentProfile(): BelongsTo
    {
        return $this->belongsTo(
            EnvironmentProfile::class,
            'environment_profile_id',
            'id'
        );
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'run_date' => 'datetime',
            'issues' => 'array',
            'status' => ParityStatus::class,
        ];
    }
}
