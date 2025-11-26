<?php

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
        Schema::create('breach_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('case_id')->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('notified_at')->nullable();
            $table->boolean('authority_notified')->default(false);
            $table->text('description');
            $table->jsonb('impact')->default('{}');
            $table->jsonb('remediation')->default('{}');
            $table->uuid('created_by');
            $table->timestamps();

            $table->index(['organization_id', 'created_at']);

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('case_id')->references('id')->on('cases')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breach_logs');
    }
};
