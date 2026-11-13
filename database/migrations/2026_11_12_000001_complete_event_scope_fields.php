<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (! Schema::hasColumn('events', 'scope_type')) {
                    $table->string('scope_type', 20)->default('global')->after('published');
                }
                if (! Schema::hasColumn('events', 'graduation_set_id')) {
                    $table->foreignId('graduation_set_id')->nullable()->after('scope_type')->constrained('graduation_sets')->nullOnDelete();
                }
                if (! Schema::hasColumn('events', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('graduation_set_id')->constrained('branches')->nullOnDelete();
                }
                if (! Schema::hasColumn('events', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('branch_id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('events', 'requires_registration')) {
                    $table->boolean('requires_registration')->default(true)->after('created_by');
                }
                if (! Schema::hasColumn('events', 'allow_non_branch_residents')) {
                    $table->boolean('allow_non_branch_residents')->default(true)->after('requires_registration');
                }
            });
        }

        if (! Schema::hasTable('event_scopes')) {
            Schema::create('event_scopes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->cascadeOnDelete();
                $table->string('scope_type', 20);
                $table->unsignedBigInteger('scope_id');
                $table->timestamps();
                $table->index(['scope_type', 'scope_id']);
                $table->unique(['event_id', 'scope_type', 'scope_id'], 'event_scope_unique');
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: this migration may be filling gaps in
        // installations where these columns/tables were added by earlier patches.
    }
};
