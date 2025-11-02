<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        return Maintenance::with(['car','supplier'])->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'service_date' => 'nullable|date',
            'service_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'reported_issue' => 'nullable|string',
            'activities_detail' => 'nullable|string',
            'pending_work' => 'nullable|string',
            'observations' => 'nullable|string',
            'inspector_name' => 'nullable|string|max:255',
            'officer_in_charge' => 'nullable|string|max:255',
            'finalized' => 'boolean',
            'finalized_at' => 'nullable|date',
        ]);
        $maintenance = Maintenance::create($data);
        return response()->json($maintenance->load(['car','supplier']), 201);
    }

    public function show(Maintenance $maintenance)
    {
        return $maintenance->load(['car','supplier','documents']);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $data = $request->validate([
            'car_id' => 'sometimes|required|exists:cars,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'service_date' => 'nullable|date',
            'service_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'reported_issue' => 'nullable|string',
            'activities_detail' => 'nullable|string',
            'pending_work' => 'nullable|string',
            'observations' => 'nullable|string',
            'inspector_name' => 'nullable|string|max:255',
            'officer_in_charge' => 'nullable|string|max:255',
            'finalized' => 'boolean',
            'finalized_at' => 'nullable|date',
        ]);
        $maintenance->update($data);
        return $maintenance->load(['car','supplier','documents']);
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return response()->noContent();
    }
}

