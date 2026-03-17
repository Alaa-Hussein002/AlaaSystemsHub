<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('created_at', 'desc');

        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('type'))   $query->where('product_type', $request->type);
        if ($request->has('category_id')) $query->where('category_id', $request->category_id);
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name.ar', 'like', "%{$s}%")
                  ->orWhere('name.en', 'like', "%{$s}%");
            });
        }

        $products = $query->get();
        return $this->success(ProductResource::collection($products));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|array',
            'slug'         => 'nullable|string|unique:products,slug',
            'product_type' => 'required|in:digital,physical',
            'pricing'      => 'required|array',
            'pricing.price'=> 'required|numeric|min:0',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']['en'] ?? $data['name']['ar']);
        }
        $data['stats'] = [
            'views_count' => 0, 'sales_count' => 0,
            'rating_average' => 0, 'rating_count' => 0, 'favorites_count' => 0,
        ];

        $product = Product::create($data);
        ActivityLog::log('create', 'products', "أضاف منتج: " . ($data['name']['ar'] ?? ''), 'product', $product->_id);
        return $this->created(new ProductResource($product));
    }

    public function show(string $id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) return $this->notFound('المنتج غير موجود');
        return $this->success(new ProductResource($product));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) return $this->notFound('المنتج غير موجود');
        $product->update($request->all());
        ActivityLog::log('update', 'products', "عدّل منتج: " . ($product->name['ar'] ?? ''), 'product', $id);
        return $this->success(new ProductResource($product), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) return $this->notFound('المنتج غير موجود');
        $title = $product->name['ar'] ?? '';
        $product->delete();
        ActivityLog::log('delete', 'products', "حذف منتج: {$title}", 'product', $id);
        return $this->success(null, 'تم الحذف');
    }
}