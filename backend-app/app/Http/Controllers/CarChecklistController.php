<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarChecklist;
use App\Models\Company;
use App\Traits\ChecksCompanyPermission;
use Illuminate\Http\Request;
use App\Models\CarChecklistItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CarChecklistController extends Controller
{
    use ChecksCompanyPermission;

    public function store(Request $request, Car $car)
    {
        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'create', 'Checklist'))) {
            return $response;
        }

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
        $company = $this->ensureCarCompany($checklist->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'update', 'Checklist'))) {
            return $response;
        }

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
            $company = $this->ensureCarCompany($checklist->car);
            if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'delete', 'Checklist'))) {
                return $response;
            }

            $checklist->items()->delete(); 
            $checklist->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar.'], 500);
        }
    }

    public function toggleItem(CarChecklistItems $item)
    {
        $company = $this->ensureCarCompany($item->checklist->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'update', 'Checklist'))) {
            return $response;
        }

        $item->completed = !$item->completed;
        $item->save();

        return response()->json($item);
    }

    private function ensureCarCompany(Car $car): ?Company
    {
        if ($car->company_id) {
            return Company::find($car->company_id);
        }

        if ($car->company) {
            $company = Company::firstOrCreate(
                ['name' => $car->company],
                ['code' => Str::slug($car->company, '-')]
            );
            if (!$company->code) {
                $company->update(['code' => Str::slug($car->company, '-')]);
            }
            $car->company_id = $company->id;
            $car->company = $company->name;
            $car->save();
            return $company;
        }

        return null;
    }
}
