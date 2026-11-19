<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_connection_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending, accepted, declined
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->unique(['requester_id', 'recipient_id']);
            $table->index(['recipient_id', 'status']);
            $table->index(['requester_id', 'status']);
        });

        Schema::create('member_chat_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['blocker_id', 'blocked_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_chat_blocks');
        Schema::dropIfExists('member_connection_requests');
    }
};
