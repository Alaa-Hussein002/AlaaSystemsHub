<?php
// database/migrations/2024_01_01_000015_create_shipping_methods_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->text('description')->nullable();
            $table->decimal('base_cost', 10, 2)->default(0);
            $table->decimal('cost_per_kg', 10, 2)->nullable();
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->unsignedInteger('estimated_days_min')->nullable();
            $table->unsignedInteger('estimated_days_max')->nullable();
            $table->json('available_zones')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};