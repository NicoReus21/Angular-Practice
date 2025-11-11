<?php
namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// Importante para la validación de 'update'

class CarController extends Controller
{
    /**
     * Muestra una lista de todas las unidades (carros).
     * GET /api/cars
     */
    public function index()
    {
        // Usamos 'with' para cargar las relaciones anidadas
        $cars = Car::with('maintenances', 'checklists.items', 'documents')
            ->orderBy('name', 'asc') // Opcional: ordenar
            ->get();

        return response()->json($cars);
    }

    /**
     * Almacena una nueva unidad (carro).
     * POST /api/cars
     */
    public function store(Request $request)
    {
        // --- ESTA ES LA PARTE IMPORTANTE ---
        // 1. Valida que los datos existan
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'plate'   => 'required|string|unique:cars,plate',
            'model'   => 'nullable|string|max:255',
            'company' => 'required|string|max:255',
            'status'  => 'required|string|max:255',
        ]);

        // 2. Si falla, devuelve un 422 (error de validación)
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Solo crea el carro si la validación pasa
        $car = Car::create($validator->validated());
        // --- FIN DE LA SECCIÓN CRÍTICA ---

        // Devolvemos el carro con sus relaciones (vacías)
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
        // Validación para actualizar
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
