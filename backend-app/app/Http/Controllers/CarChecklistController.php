<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarChecklist;
use Illuminate\Http\Request;
use App\Models\CarChecklistItems;
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


    public function update(Request $request, CarChecklist $checklist)
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
            DB::transaction(function () use ($checklist, $validatedData) {
                $checklist->update([
                    'persona_cargo' => $validatedData['persona_cargo'],
                    'fecha_realizacion' => $validatedData['fecha_realizacion'],
                ]);
                $checklist->items()->delete();
                $tasksData = [];

                foreach ($validatedData['tasks'] as $taskDescription) {
                    $tasksData[] = [
                        'task_description' => $taskDescription,
                        'completed' => false,
                    ];
                }
                $checklist->items()->createMany($tasksData);
            });

            return response()->json($checklist->load('items'), 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(CarChecklist $checklist)
    {
        try {
            $checklist->items()->delete(); 
            $checklist->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar.'], 500);
        }
    }

    public function toggleItem(CarChecklistItems $item)
    {
        $item->completed = !$item->completed;
        $item->save();

        return response()->json($item);
    }
}