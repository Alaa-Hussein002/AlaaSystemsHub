<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Invoice::orderBy('created_at', 'desc');
        if ($request->has('status')) $query->where('status', $request->status);
        $invoices = $query->get();
        return $this->success(InvoiceResource::collection($invoices));
    }

    public function show(string $invoiceNumber)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        if (!$invoice) return $this->notFound('الفاتورة غير موجودة');
        return $this->success(new InvoiceResource($invoice));
    }
}