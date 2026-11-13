<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('membership_dues_accounts', function(Blueprint $table) {
   $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
   $table->date('commencement_date')->nullable(); $table->string('plan',20)->default('annual');
   $table->unsignedInteger('annual_amount_kobo')->default(1250000); $table->unsignedInteger('monthly_amount_kobo')->default(105000);
   $table->date('paid_through')->nullable(); $table->date('next_due_at')->nullable(); $table->string('status',30)->default('due');
   $table->boolean('exempt')->default(false); $table->timestamp('last_due_notice_at')->nullable(); $table->text('admin_note')->nullable();
   $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
  });
  Schema::create('membership_dues_payments', function(Blueprint $table) {
   $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
   $table->foreignId('dues_account_id')->constrained('membership_dues_accounts')->cascadeOnDelete();
   $table->string('reference')->unique(); $table->unsignedInteger('amount_kobo'); $table->string('currency',3)->default('NGN');
   $table->string('period_type',20); $table->date('period_start')->nullable(); $table->date('period_end')->nullable();
   $table->string('method',30)->default('paystack'); $table->string('status',30)->default('pending');
   $table->string('gateway_reference')->nullable(); $table->json('gateway_response')->nullable(); $table->text('admin_note')->nullable();
   $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('paid_at')->nullable(); $table->timestamps();
   $table->index(['user_id','status']); $table->index(['status','created_at']);
  });
  Schema::create('membership_dues_audit_logs', function(Blueprint $table) {
   $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
   $table->string('action',80); $table->json('details')->nullable(); $table->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('membership_dues_audit_logs'); Schema::dropIfExists('membership_dues_payments'); Schema::dropIfExists('membership_dues_accounts'); }
};
