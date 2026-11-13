<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('absent');
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('event_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('summary')->nullable();
            $table->longText('minutes')->nullable();
            $table->string('recorded_by_name')->nullable();
            $table->timestamps();
        });

        Schema::create('event_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->text('resolution');
            $table->string('responsible_person')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_resolutions');
        Schema::dropIfExists('event_minutes');
        Schema::dropIfExists('event_attendances');
    }
};
