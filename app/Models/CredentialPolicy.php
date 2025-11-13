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

final class CredentialPolicy extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'context',
        'storage_mechanism',
        'rotation_interval_days',
        'owner',
        'notes',
    ];

    protected $keyType = 'string';

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    #[Scope]
    protected function forContext(Builder $query, string $context): Builder
    {
        return $query->where('context', $context);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rotation_interval_days' => 'integer',
        ];
    }
}
