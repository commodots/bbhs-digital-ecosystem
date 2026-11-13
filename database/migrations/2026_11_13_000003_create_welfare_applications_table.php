<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('welfare_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('category');
            $table->string('subject');
            $table->longText('description');
            $table->decimal('amount_requested', 14, 2)->nullable();
            $table->decimal('amount_approved', 14, 2)->nullable();
            $table->string('status')->default('submitted');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'category']);
        });

        Schema::create('welfare_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('welfare_application_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('storage_path');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->timestamps();
        });

        Schema::create('welfare_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('welfare_application_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('disbursed_on');
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_disbursements');
        Schema::dropIfExists('welfare_attachments');
        Schema::dropIfExists('welfare_applications');
    }
};
