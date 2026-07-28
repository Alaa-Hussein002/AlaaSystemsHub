<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\ActivityLog;
use App\Models\Certificate;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class CertificateController extends Controller
{
    use ApiResponse;

    /**
     * عرض جميع الشهادات
    */
    public function index()
    {
        try {
            $items = Certificate::orderBy('sort_order', 'asc')
                               ->orderBy('issue_date', 'desc')
                               ->get();
            
            return $this->success(CertificateResource::collection($items));

        } catch (\Exception $e) {
            Log::error('Certificate index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل جلب الشهادات',
                'error' => config('app.debug') ? $e->getMessage() : 'حدث خطأ',
            ], 500);
        }
    }

    /**
     * إنشاء شهادة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issuer_logo' => 'nullable|string',
            'certificate_image' => 'nullable|string',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:500',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'has_expiry' => 'boolean',
            'skills_gained' => 'nullable|array',
            'skills_gained.*' => 'string|max:100',
            'sort_order' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        // ✅ تنظيف مسارات الصور
        if (isset($validated['issuer_logo'])) {
            $validated['issuer_logo'] = $this->cleanImagePath($validated['issuer_logo']);
        }

        if (isset($validated['certificate_image'])) {
            $validated['certificate_image'] = $this->cleanImagePath($validated['certificate_image']);
        }

        // ✅ إذا كان has_expiry = false، احذف expiry_date
        if (!($validated['has_expiry'] ?? false)) {
            $validated['expiry_date'] = null;
        }

        $item = Certificate::create($validated);

        ActivityLog::log('create', 'certificates', "أضاف شهادة: {$item->title}");

        return $this->created(new CertificateResource($item), 'تم إنشاء الشهادة بنجاح');
    }

    /**
     * عرض شهادة واحدة
     */
    public function show(string $id)
    {
        try {
            $item = Certificate::find($id);
            
            if (!$item) {
                return $this->notFound('غير موجود');
            }
            
            return $this->success(new CertificateResource($item));

        } catch (\Exception $e) {
            Log::error('Certificate show error: ' . $e->getMessage());
            return $this->error('فشل جلب الشهادة', 500);
        }
    }

    /**
     * تحديث شهادة
     */
    public function update(Request $request, string $id)
    {
        $item = Certificate::find($id);
        
        if (!$item) {
            return $this->notFound('الشهادة غير موجودة');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'issuer' => 'sometimes|required|string|max:255',
            'issuer_logo' => 'nullable|string',
            'certificate_image' => 'nullable|string',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:500',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'has_expiry' => 'boolean',
            'skills_gained' => 'nullable|array',
            'skills_gained.*' => 'string|max:100',
            'sort_order' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        // ✅ تنظيف الصور
        if (isset($validated['issuer_logo'])) {
            $validated['issuer_logo'] = $this->cleanImagePath($validated['issuer_logo']);
        }

        if (isset($validated['certificate_image'])) {
            $validated['certificate_image'] = $this->cleanImagePath($validated['certificate_image']);
        }

        // ✅ has_expiry
        if (isset($validated['has_expiry']) && !$validated['has_expiry']) {
            $validated['expiry_date'] = null;
        }

        $item->update($validated);

        ActivityLog::log('update', 'certificates', "حدّث شهادة: {$item->title}", 'certificate', $id);

        return $this->success(new CertificateResource($item), 'تم التحديث بنجاح');
    }

    /**
     * حذف شهادة
     */
    public function destroy(string $id)
    {
        $item = Certificate::find($id);
        
        if (!$item) {
            return $this->notFound('الشهادة غير موجودة');
        }

        // ✅ حذف الصور من التخزين
        if ($item->issuer_logo && Storage::disk('public')->exists($item->issuer_logo)) {
            Storage::disk('public')->delete($item->issuer_logo);
        }

        if ($item->certificate_image && Storage::disk('public')->exists($item->certificate_image)) {
            Storage::disk('public')->delete($item->certificate_image);
        }

        $title = $item->title;
        $item->delete();

        ActivityLog::log('delete', 'certificates', "حذف شهادة: {$title}", 'certificate', $id);

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

        $path = trim($path);
    
        // ✅ روابط Cloudinary و CDN - أرجعها كما هي
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }


        // إذا كان رابط كامل، استخرج المسار النسبي
        if (preg_match('#/storage/(.+)$#', $path, $matches)) {
            return $matches[1];
        }

        // إزالة /storage/ من البداية
        return preg_replace('#^/?storage/#', '', $path);
    }
}