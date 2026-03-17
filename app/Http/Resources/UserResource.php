<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (string) $this->_id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'avatar'     => $this->avatar,
            'type'       => $this->type,
            'status'     => $this->status,
            'profile'    => $this->profile,
            'role'       => $this->whenLoaded('role', function () {
                return [
                    'id'           => (string) $this->role->_id,
                    'name'         => $this->role->name,
                    'display_name' => $this->role->display_name,
                    'permissions'  => $this->role->permissions,
                ];
            }),
            'wallet_balance' => $this->wallet_balance ?? 0,
            'last_login_at'  => $this->last_login_at?->toDateTimeString(),
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}