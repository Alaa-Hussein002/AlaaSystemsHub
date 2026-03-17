<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use App\Models\ActivityLog;
use App\Models\Certificate;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $items = Certificate::orderBy('sort_order', 'asc')->get();
        return $this->success(CertificateResource::collection($items));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string', 'issuer' => 'required|string']);
        $item = Certificate::create($request->all());
        ActivityLog::log('create', 'certificates', "أضاف شهادة: {$request->title}");
        return $this->created(new CertificateResource($item));
    }

    public function show(string $id)
    {
        $item = Certificate::find($id);
        if (!$item) return $this->notFound('غير موجود');
        return $this->success(new CertificateResource($item));
    }

    public function update(Request $request, string $id)
    {
        $item = Certificate::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->update($request->all());
        ActivityLog::log('update', 'certificates', 'عدّل شهادة', 'certificate', $id);
        return $this->success(new CertificateResource($item), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $item = Certificate::find($id);
        if (!$item) return $this->notFound('غير موجود');
        $item->delete();
        ActivityLog::log('delete', 'certificates', 'حذف شهادة', 'certificate', $id);
        return $this->success(null, 'تم الحذف');
    }
}