<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\DeletedModels\Models\DeletedModel;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sanctum:prune-expired --hours=24')->daily();

Schedule::command('model:prune', [
    '--model' => [DeletedModel::class],
])->daily();

// Telescope data retention: prune entries older than 7 days (T027c, FR-039)
Schedule::command('telescope:prune --hours='.((int) env('TELESCOPE_DB_RETENTION_DAYS', 7) * 24))->daily();
