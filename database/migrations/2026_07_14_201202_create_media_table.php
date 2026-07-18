<?php
// database/migrations/2024_01_01_000023_create_media_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_url');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->string('file_size_human');
            $table->json('dimensions')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('folder')->nullable();
            $table->string('disk')->default('public');
            $table->json('thumbnails')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('used_in')->nullable();
            $table->timestamps();
            
            $table->index('folder');
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};