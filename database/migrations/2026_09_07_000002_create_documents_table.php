<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->json('title');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 30)->default('pdf');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('version', 50)->nullable();
            $table->string('access_level', 30)->default('public');
            $table->json('allowed_roles')->nullable();
            $table->unsignedBigInteger('downloads_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('access_level');
            $table->index('is_published');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
