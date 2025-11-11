<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarChecklistController extends Controller
{

    public function store(Request $request, Car $car)
    {
        $validator = Validator::make($request->all(), [
            'persona_cargo' => 'required|string|max:255',
            'fecha_realizacion' => 'required|date',
            'tasks' => 'required|array|min:1', 
            'tasks.*' => 'required|string|max:255', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        try {
            $checklist = DB::transaction(function () use ($car, $validatedData) {
 
                $checklist = $car->checklists()->create([
                    'persona_cargo' => $validatedData['persona_cargo'],
                    'fecha_realizacion' => $validatedData['fecha_realizacion'],
                ]);

                $tasksData = [];
                foreach ($validatedData['tasks'] as $taskDescription) {
                    $tasksData[] = [
                        'task_description' => $taskDescription,
                        'completed' => false, 
                    ];
                }
                
                $checklist->items()->createMany($tasksData);

                return $checklist;
            });
            return response()->json($checklist->load('items'), 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar el checklist.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}