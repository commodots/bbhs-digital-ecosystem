<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->string('whatsapp_phone', 40)->nullable();
            $table->string('alternate_phone', 40)->nullable();
            $table->string('preferred_contact', 20)->default('whatsapp');
            $table->boolean('sms_consent')->default(false);
            $table->boolean('whatsapp_consent')->default(false);
        });
        Schema::table('fundraising_donations', function (Blueprint $table) {
            $table->string('donor_phone', 40)->nullable();
        });
        Schema::table('fundraising_pledges', function (Blueprint $table) {
            $table->string('phone', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fundraising_pledges', fn (Blueprint $table) => $table->dropColumn('phone'));
        Schema::table('fundraising_donations', fn (Blueprint $table) => $table->dropColumn('donor_phone'));
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_phone', 'alternate_phone', 'preferred_contact', 'sms_consent', 'whatsapp_consent']);
        });
    }
};
