<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class EnvironmentProfile extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'name',
        'runtime_versions',
        'prerequisites',
        'smoke_check_script',
        'status',
    ];

    protected $keyType = 'string';

    /**
     * @return HasMany<ParityResult>
     */
    public function parityResults(): HasMany
    {
        return $this->hasMany(ParityResult::class);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    #[Scope]
    protected function supported(Builder $query): Builder
    {
        return $query->where('status', 'supported');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'runtime_versions' => 'array',
            'prerequisites' => 'array',
        ];
    }
}
