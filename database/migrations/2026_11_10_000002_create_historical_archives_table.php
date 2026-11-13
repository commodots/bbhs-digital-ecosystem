<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('historical_archives', function (Blueprint $table) {
  $table->id(); $table->string('title'); $table->string('slug')->unique(); $table->string('category',80)->index();
  $table->string('subtitle')->nullable(); $table->text('summary')->nullable(); $table->longText('body')->nullable();
  $table->unsignedSmallInteger('record_year')->nullable()->index(); $table->string('source_title')->nullable();
  $table->string('source_page',100)->nullable(); $table->string('cover_image')->nullable(); $table->string('attachment_path')->nullable();
  $table->json('metadata')->nullable(); $table->boolean('is_featured')->default(false); $table->boolean('is_published')->default(false)->index();
  $table->timestamp('published_at')->nullable(); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
 }); }
 public function down(): void { Schema::dropIfExists('historical_archives'); }
};
