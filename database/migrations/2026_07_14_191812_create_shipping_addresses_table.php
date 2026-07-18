<?php
// database/migrations/2024_01_01_000014_create_shipping_addresses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('full_name');
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->string('district')->nullable();
            $table->string('street');
            $table->string('building')->nullable();
            $table->string('apartment')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('landmark')->nullable();
            $table->json('coordinates')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};