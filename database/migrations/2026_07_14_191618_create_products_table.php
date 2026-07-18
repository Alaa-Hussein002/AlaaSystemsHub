<?php
// database/migrations/2024_01_01_000011_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description');
            $table->text('short_description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->enum('product_type', ['digital', 'physical'])->default('digital');
            $table->json('pricing');
            $table->json('media')->nullable();
            $table->json('digital_asset')->nullable();
            $table->json('physical_details')->nullable();
            $table->json('attributes')->nullable();
            $table->json('tags')->nullable();
            $table->json('stats')->nullable();
            $table->json('stock')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'is_published', 'is_featured']);
            $table->index('slug');
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};