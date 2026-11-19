<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('welfare_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('welfare_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['welfare_application_id', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('welfare_activity_logs'); }
};
