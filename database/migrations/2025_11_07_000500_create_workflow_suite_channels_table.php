<?php

declare(strict_types=1);

/**
 * Compliant with [.ai/AI-GUIDELINES.md](.ai/AI-GUIDELINES.md) v3b99cda02934ad7cdc87310613fb7faac37a49f19d9620106e96e73cacb6bb8e
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_suite_channels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_suite_id')
                ->constrained('workflow_suites')
                ->cascadeOnDelete();
            $table->string('channel');
            $table->enum('medium', ['slack', 'email', 'webhook']);
            $table->timestamps();

            $table->unique(['workflow_suite_id', 'channel', 'medium'], 'workflow_suite_channels_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_suite_channels');
    }
};
