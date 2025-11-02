<?php

namespace App\Http\Controllers;

use App\Models\InvoiceMm;
use Illuminate\Http\Request;

class InvoicesMmController extends Controller
{
    public function index()
    {
        return InvoiceMm::with(['supplier','company','purchaseOrder','maintenance','payments'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'company_id' => 'nullable|exists:companies,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'maintenance_id' => 'nullable|exists:maintenances,id',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'net' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0|gte:net',
        ]);
        if ((float)$data['net'] + (float)$data['tax'] != (float)$data['total']) {
            if (abs(((float)$data['net'] + (float)$data['tax']) - (float)$data['total']) > 0.01) {
                return response()->json(['message' => 'total must equal net + tax'], 422);
            }
        }
        $invoice = InvoiceMm::create($data);
        return response()->json($invoice->load(['supplier','company','purchaseOrder','maintenance','payments']), 201);
    }

    public function show(InvoiceMm $mm_invoice)
    {
        return $mm_invoice->load(['supplier','company','purchaseOrder','maintenance','payments']);
    }

    public function update(Request $request, InvoiceMm $mm_invoice)
    {
        $data = $request->validate([
            'supplier_id' => 'sometimes|nullable|exists:suppliers,id',
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'purchase_order_id' => 'sometimes|nullable|exists:purchase_orders,id',
            'maintenance_id' => 'sometimes|nullable|exists:maintenances,id',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'net' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'alert_sent_at' => 'nullable|date',
        ]);
        $mm_invoice->update($data);
        return $mm_invoice->load(['supplier','company','purchaseOrder','maintenance','payments']);
    }

    public function destroy(InvoiceMm $mm_invoice)
    {
        $mm_invoice->delete();
        return response()->noContent();
    }
}

