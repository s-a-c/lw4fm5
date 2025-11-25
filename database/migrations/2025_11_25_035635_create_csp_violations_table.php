<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('csp_violations', function (Blueprint $table): void {
            $table->id();
            $table->string('blocked_uri')->nullable();
            $table->string('document_uri')->nullable();
            $table->string('violated_directive')->nullable();
            $table->text('original_policy')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csp_violations');
    }
};
