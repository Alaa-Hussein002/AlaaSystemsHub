<?php

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
        $request->validate(['category' => 'required|array']);
        $skill = Skill::create($request->all());
        ActivityLog::log('create', 'skills', 'أضاف مهارة جديدة');
        return $this->created(new SkillResource($skill));
    }

    public function show(string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) return $this->notFound('المهارة غير موجودة');
        return $this->success(new SkillResource($skill));
    }

    public function update(Request $request, string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) return $this->notFound('المهارة غير موجودة');
        $skill->update($request->all());
        ActivityLog::log('update', 'skills', 'عدّل مهارة', 'skill', $id);
        return $this->success(new SkillResource($skill), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) return $this->notFound('المهارة غير موجودة');
        $skill->delete();
        ActivityLog::log('delete', 'skills', 'حذف مهارة', 'skill', $id);
        return $this->success(null, 'تم الحذف');
    }
}