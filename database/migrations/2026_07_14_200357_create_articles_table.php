<?php
// database/migrations/2024_01_01_000022_create_articles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->json('blocks');
            $table->string('cover_image')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->json('sources')->nullable();
            $table->string('language', 2)->default('ar');
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('reading_time')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->json('author')->nullable();
            $table->json('seo')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index(['is_published', 'status']);
            $table->fullText(['title', 'excerpt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};