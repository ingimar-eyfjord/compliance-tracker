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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('channel_id');
            $table->string('status')->default('new');
            $table->jsonb('ciphertext');
            $table->jsonb('metadata')->default('{}');
            $table->string('created_via')->default('web');
            $table->string('reference_code')->unique();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'created_at']);

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('channel_id')->references('id')->on('channels')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
