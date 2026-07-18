<?php
// database/migrations/2024_01_01_000008_create_projects_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description');
            $table->text('short_description')->nullable();
            $table->string('category')->nullable();
            $table->json('tech_stack')->nullable();
            $table->json('features')->nullable();
            $table->json('media')->nullable();
            $table->json('links')->nullable();
            $table->enum('status', ['completed', 'in_progress', 'planned'])->default('completed');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('client')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['is_published', 'is_featured']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};