<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ToolchainDefinition extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'language',
        'version',
        'enforcement_scope',
        'verification_command',
        'documentation_url',
    ];

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'documentation_url' => 'string',
        ];
    }
}
