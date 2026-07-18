<?php
// database/migrations/2024_01_01_000003_create_personal_profiles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_profiles', function (Blueprint $table) {
            $table->id();
            $table->json('full_name');
            // $table->json('title');
            $table->json('bio');
            $table->string('photo')->nullable();
            // $table->string('cover_image')->nullable();
            $table->string('cv_file')->nullable();
            // $table->date('date_of_birth')->nullable();
            // $table->string('nationality')->nullable();
            // $table->json('location')->nullable();
            $table->json('contact')->nullable();
            $table->json('social_links')->nullable();
            $table->json('highlights')->nullable();
            $table->boolean('available_for_hire')->default(true);
            $table->string('availability_status')->nullable();
            $table->boolean('is_published')->default(true);
            $table->json('seo')->nullable();
            $table->json('rotating_roles')->nullable();
            $table->json('tech_display')->nullable();
            $table->json('tools')->nullable();
            $table->json('code_block_lines')->nullable();
            $table->string('hero_greeting')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_profiles');
    }
};