<?php

declare(strict_types=1);

/**
 * Compliant with [.ai/AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parity_results', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('environment_profile_id')->constrained('environment_profiles')->cascadeOnDelete();
            $table->timestampTz('run_date');
            $table->enum('status', ['pass', 'fail', 'warning'])->default('pass');
            $table->json('issues')->nullable();
            $table->timestamps();

            $table->index(['environment_profile_id', 'run_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parity_results');
    }
};
