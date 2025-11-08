<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ParityResult extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'environment_profile_id',
        'run_date',
        'status',
        'issues',
    ];

    protected $keyType = 'string';

    /**
     * @return BelongsTo<EnvironmentProfile, self>
     */
    public function environmentProfile(): BelongsTo
    {
        return $this->belongsTo(EnvironmentProfile::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'run_date' => 'datetime',
            'issues' => 'array',
        ];
    }
}
