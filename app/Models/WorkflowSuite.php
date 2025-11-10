<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class WorkflowSuite extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'name',
        'triggers',
        'required_checks',
        'sla_minutes',
    ];

    protected $keyType = 'string';

    /**
     * @return HasMany<WorkflowSuiteChannel>
     */
    public function channels(): HasMany
    {
        return $this->hasMany(WorkflowSuiteChannel::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'triggers' => 'array',
            'required_checks' => 'array',
            'sla_minutes' => 'integer',
        ];
    }
}
