<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_one_id', 'user_two_id']);
            $table->index(['user_one_id', 'updated_at']);
            $table->index(['user_two_id', 'updated_at']);
        });

        Schema::create('member_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('member_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_messages');
        Schema::dropIfExists('member_conversations');
    }
};
