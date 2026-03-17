<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => (string) $this->_id,
            'payment_number'  => $this->payment_number,
            'amount'          => $this->amount,
            'currency'        => $this->currency,
            'payment_method'  => $this->payment_method,
            'payment_details' => $this->payment_details,
            'status'          => $this->status,
            'status_history'  => $this->status_history,
            'confirmed_at'    => $this->confirmed_at?->toDateTimeString(),
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}