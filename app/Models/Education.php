<?php
// app/Models/Education.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    // ✅ تحديد اسم الجدول بشكل صريح
    protected $table = 'educations';

    protected $fillable = [
        'institution',
        'degree',
        'field_of_study',
        'institution_logo',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'gpa',
        'gpa_scale',
        'description',
        'courses_by_level',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'institution' => 'array',
        'degree' => 'array',
        'description' => 'array',
        'courses_by_level' => 'array',
        'is_current' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'gpa' => 'decimal:2',
        'gpa_scale' => 'decimal:2',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('start_date', 'desc');
    }

    public function getDurationAttribute()
    {
        if (!$this->start_date) {
            return null;
        }

        $start = $this->start_date;
        $end = $this->is_current ? now() : ($this->end_date ?? now());

        $years = $start->diffInYears($end);
        $months = $start->copy()->addYears($years)->diffInMonths($end);

        return [
            'years' => $years,
            'months' => $months,
            'total_months' => $start->diffInMonths($end),
        ];
    }

    /**
 * الحصول على رابط الشعار الكامل
 */
public function getInstitutionLogoUrl(): ?string
{
    if (empty($this->institution_logo)) {
        return null;
    }

    // ✅ إذا كان رابط كامل (Cloudinary)، أرجعه كما هو
    if (filter_var($this->institution_logo, FILTER_VALIDATE_URL)) {
        return $this->institution_logo;
    }

    // ✅ مسار محلي - أضف asset
    return asset('storage/' . $this->institution_logo);
}
}