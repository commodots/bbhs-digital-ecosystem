<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->boolean('public_photo')->default(true);
            $table->boolean('public_name')->default(true);
            $table->boolean('public_graduation_set')->default(true);
            $table->boolean('public_profession')->default(true);
            $table->boolean('public_location')->default(true);
            $table->boolean('public_bio')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn(['public_photo', 'public_name', 'public_graduation_set', 'public_profession', 'public_location', 'public_bio']);
        });
    }
};
