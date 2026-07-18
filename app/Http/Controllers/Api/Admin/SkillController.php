<?php
// app/Http/Controllers/Api/Admin/SkillController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\ActivityLog;
use App\Models\Skill;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $skills = Skill::orderBy('sort_order', 'asc')->get();
        return $this->success(SkillResource::collection($skills));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|array',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $data = $request->all();

        // ✅ تنظيف الأيقونة قبل الحفظ
        if (isset($data['icon'])) {
            $data['icon'] = $this->cleanIcon($data['icon']);
        }

        $skill = Skill::create($data);

        ActivityLog::log('create', 'skills', 'أضاف مهارة جديدة', 'skill', $skill->id);

        return $this->created(new SkillResource($skill));
    }

    public function show(string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) {
            return $this->notFound('المهارة غير موجودة');
        }
        return $this->success(new SkillResource($skill));
    }

    public function update(Request $request, $id)
    {
        $skill = Skill::findOrFail($id);

        $request->validate([
            'category' => 'sometimes|required|array',
        ]);

        $data = $request->all();

        // ✅ تنظيف الأيقونة
        if (isset($data['icon'])) {
            $data['icon'] = $this->cleanIcon($data['icon']);
        }

        $skill->update($data);

        ActivityLog::log('update', 'skills', 'حدّث مهارة', 'skill', $skill->id);

        return $this->success(new SkillResource($skill), 'تم التحديث بنجاح');
    }

    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        ActivityLog::log('delete', 'skills', 'حذف مهارة', 'skill', $id);

        return $this->success(null, 'تم الحذف بنجاح');
    }

    /**
     * ✅ تنظيف الأيقونة قبل الحفظ
     */
    private function cleanIcon(?string $icon): ?string
    {
        if (empty($icon)) {
            return null;
        }

        // ✅ إذا كان emoji - احفظه كما هو
        if (preg_match('/^[\x{1F000}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F300}-\x{1F5FF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}]{1,4}$/u', $icon)) {
            return $icon;
        }

        // ✅ إذا كان رابط كامل - استخرج المسار النسبي فقط
        if (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            // استخرج الجزء بعد /storage/
            if (preg_match('#/storage/(.+)$#', $icon, $matches)) {
                return $matches[1]; // مثال: media/icons/image.png
            }
        }

        // ✅ إزالة /storage/ من البداية إن وجد
        $icon = preg_replace('#^/?storage/#', '', $icon);

        return $icon;
    }
}