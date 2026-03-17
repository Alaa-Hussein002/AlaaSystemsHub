<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => (string) $this->_id,
            'items'           => $this->items ?? [],
            'items_count'     => $this->items_count ?? 0,
            'coupon_code'     => $this->coupon_code,
            'discount_amount' => $this->discount_amount ?? 0,
            'subtotal'        => $this->subtotal ?? 0,
            'total'           => $this->total ?? 0,
            'currency'        => $this->currency ?? 'USD',
        ];
    }
}