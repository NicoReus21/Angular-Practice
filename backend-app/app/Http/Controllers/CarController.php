<?php
namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\CarChecklist;
use App\Models\CarChecklistItem;
use App\Models\CarDocument;
use App\Models\Maintenance;
use App\Models\MaintenanceDocument;

// Importante para la validación de 'update'

class CarController extends Controller
{
    /**
     * Muestra una lista de todas las unidades (carros).
     * GET /api/cars
     */
    public function index()
    {
        $cars = Car::with('maintenances', 'checklists.items', 'documents')
            ->orderBy('name', 'asc') 
            ->get();

        return response()->json($cars);
    }

    /**
     * Almacena una nueva unidad (carro).
     * POST /api/cars
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'plate'   => 'required|string|unique:cars,plate',
            'model'   => 'nullable|string|max:255',
            'company' => 'required|string|max:255',
            'status'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $car = Car::create($validator->validated());
        return response()->json($car->load('maintenances', 'checklists.items', 'documents'), 201);
    }

    /**
     * Muestra una unidad (carro) específica.
     * GET /api/cars/{id}
     */
    public function show(Car $car)
    {
        return $car;
    }

    /**
     * Actualiza una unidad (carro) existente.
     * PUT/PATCH /api/cars/{id}
     */
    public function update(Request $request, Car $car)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'plate'    => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cars')->ignore($car->id),
            ],
            'model'    => 'nullable|string|max:255',
            'company'  => 'sometimes|required|string|max:255',
            'status'   => 'sometimes|required|string|max:255',
            'imageUrl' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $car->update($validator->validated());
        return response()->json($car);
    }

    /**
     * Elimina una unidad (carro).
     * DELETE /api/cars/{id}
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return response()->json(null, 204);
    }
}
