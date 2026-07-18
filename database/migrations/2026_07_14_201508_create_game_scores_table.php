<?php
// database/migrations/2024_01_01_000025_create_game_scores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('arcade_games')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('player_name')->nullable();
            $table->unsignedBigInteger('score');
            $table->unsignedInteger('level_reached')->nullable();
            $table->unsignedInteger('time_played_seconds')->nullable();
            $table->json('game_data')->nullable();
            $table->json('device_info')->nullable();
            $table->string('ip_hash')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('created_at');
            
            $table->index(['game_id', 'score']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_scores');
    }
};