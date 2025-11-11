<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarChecklistController extends Controller
{
    /**
     * Almacena un nuevo checklist (y sus tareas) para un carro.
     * POST /api/cars/{car}/checklists
     */
    public function store(Request $request, Car $car)
    {
        $validator = Validator::make($request->all(), [
            'persona_cargo' => 'required|string|max:255',
            'fecha_realizacion' => 'required|date',
            'tasks' => 'required|array|min:1', // Requerimos al menos 1 tarea
            'tasks.*' => 'required|string|max:255', // Cada tarea debe ser un string
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Usamos una transacción para asegurar que todo se guarde
        // o nada se guarde si algo falla.
        try {
            $checklist = DB::transaction(function () use ($car, $validatedData) {
                
                // 1. Crear el Checklist principal
                $checklist = $car->checklists()->create([
                    'persona_cargo' => $validatedData['persona_cargo'],
                    'fecha_realizacion' => $validatedData['fecha_realizacion'],
                ]);

                // 2. Crear las tareas (items) asociadas
                $tasksData = [];
                foreach ($validatedData['tasks'] as $taskDescription) {
                    $tasksData[] = [
                        'task_description' => $taskDescription,
                        'completed' => false, // Siempre inician sin completar
                    ];
                }
                
                $checklist->items()->createMany($tasksData);

                return $checklist;
            });

            // Devolvemos el checklist creado CON sus items
            return response()->json($checklist->load('items'), 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar el checklist.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}