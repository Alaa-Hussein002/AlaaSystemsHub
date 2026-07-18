<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            
            // البيانات الأساسية - JSON للغات المتعددة
            $table->json('institution'); // {ar: '', en: ''}
            $table->json('degree'); // {ar: '', en: ''}
            $table->string('field_of_study')->nullable();
            
            // الشعار - مسار نسبي فقط
            $table->string('institution_logo')->nullable();
            
            // الموقع والتواريخ
            $table->string('location')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            
            // المعدل
            $table->decimal('gpa', 4, 2)->nullable();
            $table->decimal('gpa_scale', 4, 2)->default(4.00);
            
            // الوصف - JSON
            $table->json('description')->nullable(); // {ar: '', en: ''}
            
            // المقررات حسب المستوى - JSON
            $table->json('courses_by_level')->nullable();
            
            // الترتيب والنشر
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index('sort_order');
            $table->index('is_published');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};