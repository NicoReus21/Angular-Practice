<?php

namespace App\Http\Controllers;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PoItemController extends Controller
{
    public function index(PurchaseOrder $purchase_order)
    {
        return $purchase_order->items()->with('car')->get();
    }

    public function store(Request $request, PurchaseOrder $purchase_order)
    {
        $data = $request->validate([
            'car_id' => 'nullable|exists:cars,id',
            'description' => 'required|string',
            'qty' => 'nullable|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'line_total' => 'nullable|numeric|min:0',
        ]);
        if (!isset($data['line_total']) && isset($data['qty'], $data['unit_price'])) {
            $data['line_total'] = (float)$data['qty'] * (float)$data['unit_price'];
        }
        $item = $purchase_order->items()->create($data);
        return response()->json($item->load('car'), 201);
    }

    public function show(PoItem $item)
    {
        return $item->load('car','purchaseOrder');
    }

    public function update(Request $request, PoItem $item)
    {
        $data = $request->validate([
            'car_id' => 'nullable|exists:cars,id',
            'description' => 'sometimes|required|string',
            'qty' => 'nullable|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'line_total' => 'nullable|numeric|min:0',
        ]);
        if (!isset($data['line_total']) && isset($data['qty'], $data['unit_price'])) {
            $data['line_total'] = (float)$data['qty'] * (float)$data['unit_price'];
        }
        $item->update($data);
        return $item->load('car','purchaseOrder');
    }

    public function destroy(PoItem $item)
    {
        $item->delete();
        return response()->noContent();
    }
}

