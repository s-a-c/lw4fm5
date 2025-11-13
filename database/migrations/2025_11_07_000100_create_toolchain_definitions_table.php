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
        Schema::create('toolchain_definitions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('language');
            $table->string('version');
            $table->enum('enforcement_scope', ['local', 'ci', 'both'])->default('both');
            $table->string('verification_command');
            $table->string('documentation_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toolchain_definitions');
    }
};
