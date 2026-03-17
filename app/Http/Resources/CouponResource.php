<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => (string) $this->_id,
            'code'                  => $this->code,
            'name'                  => $this->name,
            'description'           => $this->description,
            'discount_type'         => $this->discount_type,
            'discount_value'        => $this->discount_value,
            'minimum_order_amount'  => $this->minimum_order_amount,
            'maximum_discount_amount' => $this->maximum_discount_amount,
            'start_date'            => $this->start_date,
            'end_date'              => $this->end_date,
            'is_valid'              => $this->isValid(),
        ];
    }
}