<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('class_set')->nullable();
            $table->string('phone')->nullable();
            $table->string('profession')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('directory_visible')->default(true);
            $table->boolean('approved')->default(false);
            $table->timestamps();

            $table->index(['class_set', 'country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
