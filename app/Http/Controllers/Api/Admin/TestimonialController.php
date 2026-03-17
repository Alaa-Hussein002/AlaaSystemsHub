<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\ActivityLog;
use App\Models\Testimonial;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $items = Testimonial::orderBy('sort_order', 'asc')->get();
        return $this->success(TestimonialResource::collection($items));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string',
            'content'     => 'required|array',
            'rating'      => 'required|integer|min:1|max:5',
        ]);
        $item = Testimonial::create($request->all());
        ActivityLog::log('create', 'testimonials', "أضاف توصية من: {$request->client_name}");
        return $this->created(new TestimonialResource($item));
    }

    public function show(string $id)
    {
        $item = Testimonial::find($id);
        if (!$item) return $this->notFound('غير موجود');
        return $this->success(new TestimonialResource($item));
    }

    public function update(Request $request, string $id)
    {
        $item = Testimonial::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->update($request->all());
        ActivityLog::log('update', 'testimonials', 'عدّل توصية', 'testimonial', $id);
        return $this->success(new TestimonialResource($item), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $item = Testimonial::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->delete();
        ActivityLog::log('delete', 'testimonials', 'حذف توصية', 'testimonial', $id);
        return $this->success(null, 'تم الحذف');
    }
}