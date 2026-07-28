<?php
// app/Http/Resources/CertificateResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            
            // البيانات الأساسية
            'title' => $this->title,
            'issuer' => $this->issuer,
            
        // ✅ معالجة الصور
        'issuer_logo' => $this->getImageUrl($this->issuer_logo),
        'certificate_image' => $this->getImageUrl($this->certificate_image),
            
            // بيانات الاعتماد
            'credential_id' => $this->credential_id,
            'credential_url' => $this->credential_url,
            
            // التواريخ
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'has_expiry' => $this->has_expiry ?? false,
            
            // ✅ حالة الانتهاء
            'is_expired' => $this->is_expired,
            'days_until_expiry' => $this->days_until_expiry,
            
            // المهارات
            'skills_gained' => $this->skills_gained ?? [],
            
            // الترتيب والنشر
            'sort_order' => $this->sort_order ?? 0,
            'is_published' => $this->is_published ?? true,
            
            // التواريخ
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * الحصول على رابط الصورة الصحيح
     */
    private function getImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
    
        // رابط كامل
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
    
        // مسار محلي
        return asset('storage/' . $path);
    }
}