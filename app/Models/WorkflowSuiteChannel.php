<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WorkflowSuiteChannel extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'workflow_suite_id',
        'channel',
        'medium',
    ];

    protected $keyType = 'string';

    /**
     * @return BelongsTo<WorkflowSuite, self>
     */
    public function workflowSuite(): BelongsTo
    {
        return $this->belongsTo(WorkflowSuite::class);
    }
}
