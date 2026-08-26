<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('member')->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'member_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('member_status')->default('pending')->after('role');
            });
        }
    }

    public function down(): void
    {
        $columns = [];
        if (Schema::hasColumn('users', 'member_status')) $columns[] = 'member_status';
        if (Schema::hasColumn('users', 'role')) $columns[] = 'role';

        if ($columns) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
