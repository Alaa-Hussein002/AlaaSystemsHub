<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = User::where('type', 'admin')->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }
        $users = $query->with('role')->get();
        return $this->success(UserResource::collection($users));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|min:2',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|string',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'type'     => 'admin',
            'role_id'  => $request->role_id,
            'status'   => 'active',
            'wallet_balance' => 0,
        ]);

        $user->load('role');
        ActivityLog::log('create', 'users', "أضاف مستخدم إداري: {$request->name}");
        return $this->created(new UserResource($user));
    }

    public function show(string $id)
    {
        $user = User::with('role')->find($id);
        if (!$user) return $this->notFound('المستخدم غير موجود');
        return $this->success(new UserResource($user));
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) return $this->notFound('المستخدم غير موجود');

        $data = $request->only(['name', 'phone', 'role_id', 'status']);
        if ($request->has('password') && $request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->load('role');
        ActivityLog::log('update', 'users', "عدّل مستخدم: {$user->name}", 'user', $id);
        return $this->success(new UserResource($user), 'تم التحديث');
    }

    public function destroy(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) return $this->notFound('المستخدم غير موجود');

        if ((string) $user->_id === (string) $request->user()->_id) {
            return $this->error('لا يمكنك حذف حسابك');
        }

        $user->delete();
        ActivityLog::log('delete', 'users', "حذف مستخدم: {$user->name}", 'user', $id);
        return $this->success(null, 'تم الحذف');
    }
}