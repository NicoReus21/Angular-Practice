<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    /**
     * Almacena un nuevo reporte de mantención para un carro específico.
     * POST /api/cars/{car}/maintenances
     */
    public function store(Request $request, Car $car)
    {
        // Validamos los datos del formulario 'create-report'
        $validator = Validator::make($request->all(), [
            'chassis_number' => 'nullable|string|max:255',
            'mileage' => 'required|integer',
            'cabin' => 'nullable|string|max:255',
            'filter_code' => 'nullable|string|max:255',
            'hourmeter' => 'nullable|string|max:255',
            'warnings' => 'nullable|string',
            'service_type' => 'required|string|max:255',
            'inspector_name' => 'required|string|max:255',
            'service_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'reported_problem' => 'required|string',
            'activities_detail' => 'required|string',
            'pending_work' => 'nullable|string',
            'pending_type' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'inspector_signature' => 'required|string|max:255',
            'officer_signature' => 'required|string|max:255',
            'car_info_annex' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Creamos la mantención y la asociamos al carro
        $maintenance = $car->maintenances()->create($validator->validated());

        return response()->json($maintenance, 201);
    }

    // Nota: Aún no hemos implementado el borrado o subida de archivos,
    // pero este controlador ya guarda toda la data de texto.
}