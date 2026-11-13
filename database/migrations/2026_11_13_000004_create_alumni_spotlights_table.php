<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alumni_spotlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->string('headline', 160)->nullable();
            $table->text('writeup');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique('alumni_profile_id');
            $table->index(['is_active', 'sort_order']);
        });
    }
    public function down(): void { Schema::dropIfExists('alumni_spotlights'); }
};
