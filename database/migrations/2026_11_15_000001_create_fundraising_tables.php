<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('fundraising_campaigns', function(Blueprint $table) {
   $table->id(); $table->string('title'); $table->string('slug')->unique(); $table->string('category',100)->nullable();
   $table->string('scope_type',20)->default('national'); $table->unsignedBigInteger('scope_id')->nullable();
   $table->text('excerpt')->nullable(); $table->longText('description')->nullable(); $table->string('image')->nullable();
   $table->unsignedBigInteger('target_amount_kobo')->default(0); $table->date('starts_at')->nullable(); $table->date('ends_at')->nullable();
   $table->boolean('published')->default(false); $table->string('status',30)->default('draft');
   $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
   $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('approved_at')->nullable();
   $table->timestamps(); $table->index(['published','status']); $table->index(['scope_type','scope_id']);
  });
  Schema::create('fundraising_donations', function(Blueprint $table) {
   $table->id(); $table->foreignId('campaign_id')->constrained('fundraising_campaigns')->cascadeOnDelete();
   $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
   $table->string('donor_name')->nullable(); $table->string('donor_email'); $table->unsignedBigInteger('amount_kobo');
   $table->string('currency',3)->default('NGN'); $table->string('reference')->unique(); $table->string('method',30)->default('paystack');
   $table->string('status',30)->default('pending'); $table->boolean('is_anonymous')->default(false);
   $table->string('gateway_reference')->nullable(); $table->json('gateway_response')->nullable();
   $table->string('transfer_reference')->nullable(); $table->text('admin_note')->nullable();
   $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('paid_at')->nullable(); $table->timestamps();
   $table->index(['campaign_id','status']); $table->index(['status','created_at']);
  });
  Schema::create('fundraising_pledges', function(Blueprint $table) {
   $table->id(); $table->foreignId('campaign_id')->constrained('fundraising_campaigns')->cascadeOnDelete();
   $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
   $table->string('name'); $table->string('email'); $table->unsignedBigInteger('amount_kobo');
   $table->text('note')->nullable(); $table->string('status',20)->default('open'); $table->timestamp('fulfilled_at')->nullable(); $table->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('fundraising_pledges'); Schema::dropIfExists('fundraising_donations'); Schema::dropIfExists('fundraising_campaigns'); }
};
