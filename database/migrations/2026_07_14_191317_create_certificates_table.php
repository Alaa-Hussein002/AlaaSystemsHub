<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            
            // البيانات الأساسية
            $table->string('title');
            $table->string('issuer');
            
            // الصور - مسارات نسبية فقط
            $table->string('issuer_logo')->nullable();
            $table->string('certificate_image')->nullable();
            
            // بيانات الاعتماد
            $table->string('credential_id')->nullable();
            $table->text('credential_url')->nullable();
            
            // التواريخ
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('has_expiry')->default(false);
            
            // المهارات المكتسبة - JSON
            $table->json('skills_gained')->nullable();
            
            // الترتيب والنشر
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('sort_order');
            $table->index('is_published');
            $table->index('issue_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};