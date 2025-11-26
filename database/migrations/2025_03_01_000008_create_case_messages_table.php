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
        Schema::create('case_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('case_id');
            $table->string('sender_type');
            $table->uuid('sender_user_id')->nullable();
            $table->jsonb('ciphertext');
            $table->jsonb('attachments')->default('[]');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['case_id', 'created_at']);

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('case_id')->references('id')->on('cases')->cascadeOnDelete();
            $table->foreign('sender_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_messages');
    }
};
