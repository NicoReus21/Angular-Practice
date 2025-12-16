<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; 

class CarController extends Controller
{
    /**
     * Muestra una lista de todas las unidades.
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
     * Almacena una nueva unidad.
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
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('car_images', 'public');
            $url = Storage::disk('public')->url($path);
            $validatedData['imageUrl'] = $url;
        }
        unset($validatedData['image']);
        $car = Car::create($validatedData);
        
        return response()->json($car->load('maintenances', 'checklists.items', 'documents'), 201);
    }

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
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        if ($request->hasFile('image')) {
            if ($car->imageUrl) {
                $oldPath = ltrim(str_replace('/storage', '', parse_url($car->imageUrl, PHP_URL_PATH)), '/');
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('car_images', 'public');
            $validatedData['imageUrl'] = Storage::disk('public')->url($path);
        }

        unset($validatedData['image']);

        $car->update($validatedData);
        
        return response()->json($car);
    }

    public function destroy(Car $car)
    {
        if ($car->imageUrl) {
            $path = ltrim(str_replace('/storage', '', parse_url($car->imageUrl, PHP_URL_PATH)), '/');
            Storage::disk('public')->delete($path);
        }
        
        $car->delete();

        return response()->json(null, 204);
    }
}
