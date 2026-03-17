<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $roles = Role::all();
        return $this->success($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|unique:roles,name',
            'display_name' => 'required|string',
            'permissions'  => 'required|array',
        ]);

        $role = Role::create($request->all());
        ActivityLog::log('create', 'users', "أضاف دور: {$request->display_name}");
        return $this->created($role);
    }

    public function show(string $id)
    {
        $role = Role::find($id);
        if (!$role) return $this->notFound('الدور غير موجود');
        return $this->success($role);
    }

    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
        if (!$role) return $this->notFound('الدور غير موجود');
        if ($role->is_system && $role->name === 'super_admin') {
            return $this->error('لا يمكن تعديل دور المدير الأعلى');
        }
        $role->update($request->all());
        ActivityLog::log('update', 'users', "عدّل دور: {$role->display_name}", 'role', $id);
        return $this->success($role, 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (!$role) return $this->notFound('الدور غير موجود');
        if ($role->is_system) return $this->error('لا يمكن حذف دور النظام');
        if ($role->users()->count() > 0) return $this->error('لا يمكن حذف دور مرتبط بمستخدمين');
        $role->delete();
        ActivityLog::log('delete', 'users', "حذف دور: {$role->display_name}", 'role', $id);
        return $this->success(null, 'تم الحذف');
    }

    public function availablePermissions()
    {
        return $this->success(Role::availableModules(), 'الصلاحيات المتاحة');
    }
}