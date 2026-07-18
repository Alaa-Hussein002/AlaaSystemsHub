<?php
// database/migrations/2024_01_01_000024_create_arcade_games_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arcade_games', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('game_type');
            $table->string('difficulty')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('screenshots')->nullable();
            $table->json('game_config')->nullable();
            $table->json('controls')->nullable();
            $table->json('stats')->nullable();
            $table->json('rewards')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arcade_games');
    }
};