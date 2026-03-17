<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\ActivityLog;
use App\Models\Coupon;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return $this->success(CouponResource::collection($coupons));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'           => 'required|string|unique:coupons,code',
            'name'           => 'required|string',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);
        $data['used_count'] = 0;

        $coupon = Coupon::create($data);
        ActivityLog::log('create', 'coupons', "أضاف كوبون: {$data['code']}");
        return $this->created(new CouponResource($coupon));
    }

    public function show(string $id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) return $this->notFound('الكوبون غير موجود');
        return $this->success(new CouponResource($coupon));
    }

    public function update(Request $request, string $id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) return $this->notFound('الكوبون غير موجود');
        $coupon->update($request->all());
        ActivityLog::log('update', 'coupons', "عدّل كوبون: {$coupon->code}", 'coupon', $id);
        return $this->success(new CouponResource($coupon), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) return $this->notFound('الكوبون غير موجود');
        $coupon->delete();
        ActivityLog::log('delete', 'coupons', "حذف كوبون: {$coupon->code}", 'coupon', $id);
        return $this->success(null, 'تم الحذف');
    }
}