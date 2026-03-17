<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\ActivityLog;
use App\Models\Experience;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $items = Experience::orderBy('sort_order', 'asc')->get();
        return $this->success(ExperienceResource::collection($items));
    }

    public function store(Request $request)
    {
        $request->validate(['company' => 'required|array', 'position' => 'required|array']);
        $item = Experience::create($request->all());
        ActivityLog::log('create', 'experiences', 'أضاف خبرة جديدة');
        return $this->created(new ExperienceResource($item));
    }

    public function show(string $id)
    {
        $item = Experience::find($id);
        if (!$item) return $this->notFound('غير موجود');
        return $this->success(new ExperienceResource($item));
    }

    public function update(Request $request, string $id)
    {
        $item = Experience::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->update($request->all());
        ActivityLog::log('update', 'experiences', 'عدّل خبرة', 'experience', $id);
        return $this->success(new ExperienceResource($item), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $item = Experience::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->delete();
        ActivityLog::log('delete', 'experiences', 'حذف خبرة', 'experience', $id);
        return $this->success(null, 'تم الحذف');
    }
}