<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => (string) $this->_id,
            'invoice_number'  => $this->invoice_number,
            'seller_info'     => $this->seller_info,
            'buyer_info'      => $this->buyer_info,
            'items'           => $this->items,
            'subtotal'        => $this->subtotal,
            'discount_total'  => $this->discount_total,
            'tax_total'       => $this->tax_total,
            'grand_total'     => $this->grand_total,
            'currency'        => $this->currency,
            'status'          => $this->status,
            'issue_date'      => $this->issue_date,
            'paid_date'       => $this->paid_date,
            'pdf_url'         => $this->pdf_url,
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}