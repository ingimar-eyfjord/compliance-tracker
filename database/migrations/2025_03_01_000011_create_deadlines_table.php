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
        Schema::create('deadlines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('case_id');
            $table->string('type');
            $table->string('status')->default('open');
            $table->timestamp('due_at');
            $table->timestamp('met_at')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();

            $table->unique(['case_id', 'type']);
            $table->index(['organization_id', 'status']);

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('case_id')->references('id')->on('cases')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadlines');
    }
};
