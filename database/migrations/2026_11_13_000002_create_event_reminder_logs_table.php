<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('reminder_days');
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->unique(['event_id', 'user_id', 'reminder_days'], 'event_reminder_once');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_reminder_logs');
    }
};
