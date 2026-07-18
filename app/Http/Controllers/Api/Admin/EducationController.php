<?php
// app/Http/Controllers/Api/Admin/EducationController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationResource;
use App\Models\ActivityLog;
use App\Models\Education;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EducationController extends Controller
{
    use ApiResponse;

    /**
     * عرض جميع المؤهلات
     */
    public function index()
    {
        $items = Education::orderBy('sort_order', 'asc')
                         ->orderBy('start_date', 'desc')
                         ->get();
        
        return $this->success(EducationResource::collection($items));
    }

    /**
     * إنشاء مؤهل جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|array',
            'institution.ar' => 'required|string|max:255',
            'institution.en' => 'nullable|string|max:255',
            'degree' => 'required|array',
            'degree.ar' => 'required|string|max:255',
            'degree.en' => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'institution_logo' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'gpa_scale' => 'nullable|numeric|min:0|max:5',
            'description' => 'nullable|array',
            'courses_by_level' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        // ✅ تنظيف مسار الشعار
        if (isset($validated['institution_logo'])) {
            $validated['institution_logo'] = $this->cleanImagePath($validated['institution_logo']);
        }

        // ✅ إذا كان is_current = true، احذف end_date
        if ($validated['is_current'] ?? false) {
            $validated['end_date'] = null;
        }

        $item = Education::create($validated);

        ActivityLog::log('create', 'educations', 'أضاف مؤهل تعليمي: ' . ($item->degree['ar'] ?? ''));

        return $this->created(new EducationResource($item), 'تم إنشاء المؤهل بنجاح');
    }

    /**
     * عرض مؤهل واحد
     */
    public function show(string $id)
    {
        $item = Education::find($id);
        
        if (!$item) {
            return $this->notFound('المؤهل غير موجود');
        }

        return $this->success(new EducationResource($item));
    }

    /**
     * تحديث مؤهل
     */
    public function update(Request $request, string $id)
    {
        $item = Education::find($id);
        
        if (!$item) {
            return $this->notFound('المؤهل غير موجود');
        }

        $validated = $request->validate([
            'institution' => 'sometimes|required|array',
            'institution.ar' => 'sometimes|required|string|max:255',
            'institution.en' => 'nullable|string|max:255',
            'degree' => 'sometimes|required|array',
            'degree.ar' => 'sometimes|required|string|max:255',
            'degree.en' => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'institution_logo' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'gpa_scale' => 'nullable|numeric|min:0|max:5',
            'description' => 'nullable|array',
            'courses_by_level' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        // ✅ تنظيف الشعار
        if (isset($validated['institution_logo'])) {
            $validated['institution_logo'] = $this->cleanImagePath($validated['institution_logo']);
        }

        // ✅ is_current
        if (isset($validated['is_current']) && $validated['is_current']) {
            $validated['end_date'] = null;
        }

        $item->update($validated);

        ActivityLog::log('update', 'educations', 'حدّث مؤهل تعليمي: ' . ($item->degree['ar'] ?? ''), 'education', $id);

        return $this->success(new EducationResource($item), 'تم التحديث بنجاح');
    }

    /**
     * حذف مؤهل
     */
    public function destroy(string $id)
    {
        $item = Education::find($id);
        
        if (!$item) {
            return $this->notFound('المؤهل غير موجود');
        }

        // ✅ حذف الشعار من التخزين
        if ($item->institution_logo && Storage::disk('public')->exists($item->institution_logo)) {
            Storage::disk('public')->delete($item->institution_logo);
        }

        $degreeName = $item->degree['ar'] ?? $item->degree['en'] ?? 'مؤهل';
        $item->delete();

        ActivityLog::log('delete', 'educations', 'حذف مؤهل تعليمي: ' . $degreeName, 'education', $id);

        return $this->success(null, 'تم الحذف بنجاح');
    }

    /**
     * ✅ تنظيف مسار الصورة
     */
    private function cleanImagePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // إذا كان رابط كامل، استخرج المسار النسبي
        if (preg_match('#/storage/(.+)$#', $path, $matches)) {
            return $matches[1];
        }

        // إزالة /storage/ من البداية
        return preg_replace('#^/?storage/#', '', $path);
    }
}