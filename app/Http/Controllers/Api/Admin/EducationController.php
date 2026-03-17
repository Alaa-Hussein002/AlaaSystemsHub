<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationResource;
use App\Models\ActivityLog;
use App\Models\Education;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $items = Education::orderBy('sort_order', 'asc')->get();
        return $this->success(EducationResource::collection($items));
    }

    public function store(Request $request)
    {
        $request->validate(['institution' => 'required|array', 'degree' => 'required|array']);
        $item = Education::create($request->all());
        ActivityLog::log('create', 'educations', 'أضاف مؤهل تعليمي');
        return $this->created(new EducationResource($item));
    }

    public function show(string $id)
    {
        $item = Education::find($id);
        if (!$item) return $this->notFound('غير موجود');
        return $this->success(new EducationResource($item));
    }

    public function update(Request $request, string $id)
    {
        $item = Education::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->update($request->all());
        ActivityLog::log('update', 'educations', 'عدّل مؤهل تعليمي', 'education', $id);
        return $this->success(new EducationResource($item), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $item = Education::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->delete();
        ActivityLog::log('delete', 'educations', 'حذف مؤهل تعليمي', 'education', $id);
        return $this->success(null, 'تم الحذف');
    }
}