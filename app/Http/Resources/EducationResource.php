<?php
// app/Http/Resources/EducationResource.php

namespace App\Http\Resources;

use App\Helpers\IconHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            
            // البيانات متعددة اللغات
            'institution' => $this->institution ?? ['ar' => '', 'en' => ''],
            'degree' => $this->degree ?? ['ar' => '', 'en' => ''],
            'field_of_study' => $this->field_of_study,
            
            // ✅ الشعار - رابط كامل
            'institution_logo' => $this->institution_logo 
                ? url('storage/' . $this->institution_logo) 
                : null,
            
            // التواريخ والموقع
            'location' => $this->location,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_current' => $this->is_current ?? false,
            
            // المعدل
            'gpa' => $this->gpa ? (float) $this->gpa : null,
            'gpa_scale' => $this->gpa_scale ? (float) $this->gpa_scale : 4.0,
            
            // الوصف
            'description' => $this->description ?? ['ar' => '', 'en' => ''],
            
            // المقررات
            'courses_by_level' => $this->courses_by_level ?? [],
            
            // ✅ المدة المحسوبة تلقائياً
            'duration' => $this->duration,
            
            // الترتيب والنشر
            'sort_order' => $this->sort_order ?? 0,
            'is_published' => $this->is_published ?? true,
            
            // التواريخ
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}