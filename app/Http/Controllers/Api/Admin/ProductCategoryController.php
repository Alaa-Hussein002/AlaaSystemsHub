<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ActivityLog;
use App\Models\ProductCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->orderBy('sort_order', 'asc')
            ->with('children')
            ->get();
        return $this->success(ProductCategoryResource::collection($categories));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',
            'slug' => 'nullable|string|unique:product_categories,slug',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']['en'] ?? $data['name']['ar']);
        }
        $data['products_count'] = 0;

        $category = ProductCategory::create($data);
        ActivityLog::log('create', 'products', 'أضاف تصنيف منتجات');
        return $this->created(new ProductCategoryResource($category));
    }

    public function show(string $id)
    {
        $category = ProductCategory::with('children')->find($id);
        if (!$category) return $this->notFound('التصنيف غير موجود');
        return $this->success(new ProductCategoryResource($category));
    }

    public function update(Request $request, string $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) return $this->notFound('التصنيف غير موجود');
        $category->update($request->all());
        ActivityLog::log('update', 'products', 'عدّل تصنيف', 'category', $id);
        return $this->success(new ProductCategoryResource($category), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) return $this->notFound('التصنيف غير موجود');
        if ($category->products()->count() > 0) {
            return $this->error('لا يمكن حذف تصنيف يحتوي على منتجات');
        }
        $category->delete();
        ActivityLog::log('delete', 'products', 'حذف تصنيف', 'category', $id);
        return $this->success(null, 'تم الحذف');
    }
}