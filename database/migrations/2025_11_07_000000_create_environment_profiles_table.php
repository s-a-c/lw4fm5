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
        Schema::create('environment_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->json('runtime_versions');
            $table->json('prerequisites')->nullable();
            $table->string('smoke_check_script')->nullable();
            $table->enum('status', ['supported', 'deprecated'])->default('supported');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environment_profiles');
    }
};
