<?php
// database/migrations/2024_01_01_000030_create_analytics_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('event_category');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_title')->nullable();
            $table->json('visitor')->nullable();
            $table->json('device')->nullable();
            $table->json('referrer')->nullable();
            $table->string('page_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            
            $table->index(['event_type', 'created_at']);
            $table->index('target_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};