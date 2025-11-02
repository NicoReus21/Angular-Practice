<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index() { return Supplier::orderBy('name')->get(); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
        ]);
        $supplier = Supplier::create($data);
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier) { return $supplier; }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
        ]);
        $supplier->update($data);
        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->noContent();
    }
}

