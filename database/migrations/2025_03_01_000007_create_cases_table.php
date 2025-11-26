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
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('report_id')->unique();
            $table->uuid('assignee_user_id')->nullable();
            $table->string('status')->default('new');
            $table->string('priority')->default('medium');
            $table->timestamp('due_at')->nullable();
            $table->jsonb('tags')->default('[]');
            $table->jsonb('properties')->default('{}');
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['assignee_user_id', 'status']);

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('report_id')->references('id')->on('reports')->cascadeOnDelete();
            $table->foreign('assignee_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
