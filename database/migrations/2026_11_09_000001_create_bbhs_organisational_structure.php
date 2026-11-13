<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduation_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('graduation_year')->unique();
            $table->string('name', 40)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('branch_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('city');
            $table->string('country')->default('Nigeria');
            $table->string('city_normalized', 150);
            $table->string('country_normalized', 150);
            $table->timestamps();
            $table->unique(['branch_id', 'city_normalized', 'country_normalized'], 'branch_city_unique');
            $table->index(['city_normalized', 'country_normalized'], 'branch_city_lookup');
        });

        Schema::create('executive_positions', function (Blueprint $table) {
            $table->id();
            $table->string('scope_type', 20); // global, set, branch
            $table->string('name');
            $table->string('code');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['scope_type', 'code']);
            $table->index(['scope_type', 'is_active']);
        });

        Schema::create('leadership_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executive_position_id')->constrained()->restrictOnDelete();
            $table->string('scope_type', 20); // global, set, branch
            $table->unsignedBigInteger('scope_id')->nullable(); // null for global; set/branch ID otherwise
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('appointed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['scope_type', 'scope_id', 'is_active'], 'leadership_scope_active');
            $table->index(['user_id', 'is_active']);
        });

        Schema::create('member_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, left
            $table->boolean('is_primary')->default(true);
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'branch_id']);
            $table->index(['branch_id', 'status']);
        });

        Schema::create('membership_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('verification_type', 20); // set, branch
            $table->string('scope_type', 20); // set, branch
            $table->unsignedBigInteger('scope_id');
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, needs_information
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->index(['verification_type', 'scope_type', 'scope_id', 'status'], 'verification_queue_lookup');
            $table->index(['user_id', 'verification_type']);
        });

        Schema::create('membership_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60);
            $table->string('scope_type', 20)->nullable();
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['scope_type', 'scope_id', 'action']);
        });

        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->foreignId('graduation_set_id')->nullable()->after('class_set')
                ->constrained('graduation_sets')->nullOnDelete();
            $table->string('city_normalized', 150)->nullable()->after('city')->index();
            $table->string('country_normalized', 150)->nullable()->after('country')->index();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('scope_type', 20)->default('global')->after('published');
            $table->foreignId('graduation_set_id')->nullable()->after('scope_type')
                ->constrained('graduation_sets')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('graduation_set_id')
                ->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->after('branch_id')
                ->constrained('users')->nullOnDelete();
            $table->boolean('requires_registration')->default(true)->after('created_by');
            $table->boolean('allow_non_branch_residents')->default(true)->after('requires_registration');
            $table->index(['scope_type', 'graduation_set_id', 'published'], 'events_set_visibility');
            $table->index(['scope_type', 'branch_id', 'published'], 'events_branch_visibility');
        });

        Schema::create('event_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('scope_type', 20); // set or branch
            $table->unsignedBigInteger('scope_id');
            $table->timestamps();
            $table->unique(['event_id', 'scope_type', 'scope_id'], 'event_scope_unique');
            $table->index(['scope_type', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_scopes');
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_set_visibility');
            $table->dropIndex('events_branch_visibility');
            $table->dropConstrainedForeignId('graduation_set_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['scope_type', 'requires_registration', 'allow_non_branch_residents']);
        });
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('graduation_set_id');
            $table->dropColumn(['city_normalized', 'country_normalized']);
        });
        Schema::dropIfExists('membership_audit_logs');
        Schema::dropIfExists('membership_verifications');
        Schema::dropIfExists('member_branches');
        Schema::dropIfExists('leadership_assignments');
        Schema::dropIfExists('executive_positions');
        Schema::dropIfExists('branch_cities');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('graduation_sets');
    }
};
