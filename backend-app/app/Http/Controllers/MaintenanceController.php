<?php
namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Maintenance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    /**
     * Almacena un nuevo reporte de mantención y genera un PDF.
     * POST /api/cars/{car}/maintenances
     */
    // app/Http/Controllers/MaintenanceController.php

    public function store(Request $request, Car $car)
    {
        $status = $request->input('status', 'completed');

        $rules = [
            'service_date'   => 'required|date',           
            'inspector_name' => 'nullable|string|max:255',
            
        ];

        if ($status === 'completed') {
            $rules['inspector_name']      = 'required|string|max:255';
            $rules['mileage']             = 'required|integer';
            $rules['service_type']        = 'required|string|max:255';
            $rules['reported_problem']    = 'required|string';
            $rules['activities_detail']   = 'required|string';
            $rules['inspector_signature'] = 'required|string'; 
            $rules['officer_signature']   = 'required|string';


        }
        else {
            $rules['mileage']             = 'nullable|integer';
            $rules['service_type']        = 'nullable|string|max:255';
            $rules['reported_problem']    = 'nullable|string';
            $rules['activities_detail']   = 'nullable|string';
            $rules['inspector_signature'] = 'nullable|string';
            $rules['officer_signature']   = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData           = $validator->validated();
        $validatedData['status'] = $status; 
        $maintenance = $car->maintenances()->create($validatedData);

        if ($status === 'completed') {
            $pdf      = Pdf::loadView('pdfs.report', ['maintenance' => $maintenance]);
            $filename = 'reports/reporte-unidad-' . $car->id . '-' . $maintenance->id . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());

            $maintenance->pdf_url = Storage::url($filename);
            $maintenance->save();
        }

        return response()->json($maintenance, 201);
    }

    public function destroy(Maintenance $maintenance)
    {
        try {
            if ($maintenance->pdf_url) {
                $path = str_replace(Storage::url(''), 'public/', $maintenance->pdf_url);

                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            $maintenance->delete();
            return response()->json(['message' => 'Reporte eliminado con éxito.'], 200);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar maintenance: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el reporte.'], 500);
        }
    }
}
