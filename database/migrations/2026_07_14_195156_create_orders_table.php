<?php
// database/migrations/2024_01_01_000016_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('customer_info');
            $table->json('items');
            $table->json('pricing');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('order_status', [
                'pending', 'confirmed', 'processing', 
                'shipped', 'delivered', 'completed', 'cancelled'
            ])->default('pending');
            $table->json('status_history')->nullable();
            $table->json('shipping_address')->nullable();
            $table->string('shipping_method')->nullable();
            $table->foreignId('shipment_id')->nullable();
            $table->json('notes')->nullable();
            $table->boolean('is_gift')->default(false);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index('order_number');
            $table->index(['user_id', 'order_status']);
            $table->index(['payment_status', 'order_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};