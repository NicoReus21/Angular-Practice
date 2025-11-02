<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return PurchaseOrder::with(['company','supplier','items'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'issue_date' => 'nullable|date',
            'net' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0|gte:net',
        ]);
        if ((float)$data['net'] + (float)$data['tax'] != (float)$data['total']) {
            // Allow small float differences
            if (abs(((float)$data['net'] + (float)$data['tax']) - (float)$data['total']) > 0.01) {
                return response()->json(['message' => 'total must equal net + tax'], 422);
            }
        }
        $po = PurchaseOrder::create($data);
        return response()->json($po->load(['company','supplier','items']), 201);
    }

    public function show(PurchaseOrder $purchase_order)
    {
        return $purchase_order->load(['company','supplier','items']);
    }

    public function update(Request $request, PurchaseOrder $purchase_order)
    {
        $data = $request->validate([
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'supplier_id' => 'sometimes|nullable|exists:suppliers,id',
            'issue_date' => 'nullable|date',
            'net' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
        ]);
        $purchase_order->update($data);
        return $purchase_order->load(['company','supplier','items']);
    }

    public function destroy(PurchaseOrder $purchase_order)
    {
        $purchase_order->delete();
        return response()->noContent();
    }
}

