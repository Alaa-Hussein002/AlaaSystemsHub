<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => (string) $this->_id,
            'order_number'    => $this->order_number,
            'customer_info'   => $this->customer_info,
            'items'           => $this->items,
            'pricing'         => $this->pricing,
            'payment_method'  => $this->payment_method,
            'payment_status'  => $this->payment_status,
            'order_status'    => $this->order_status,
            'status_history'  => $this->status_history,
            'notes'           => $this->notes,
            'payments'        => PaymentResource::collection($this->whenLoaded('payments')),
            'invoice'         => new InvoiceResource($this->whenLoaded('invoice')),
            'created_at'      => $this->created_at?->toDateTimeString(),
            'completed_at'    => $this->completed_at?->toDateTimeString(),
        ];
    }
}