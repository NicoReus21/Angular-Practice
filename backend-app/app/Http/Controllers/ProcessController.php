<?php
namespace App\Http\Controllers;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessController extends Controller
{
    
    public function index()
    {
        $processes = Process::with('documents')->orderBy('created_at', 'desc')->get();
        return response()->json($processes);
    }

    /**
     * Almacena un nuevo proceso en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bombero_name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
        ]);

        $process = Process::create([
            'bombero_name' => $validatedData['bombero_name'],
            'company' => $validatedData['company'],
            'sections_data' => '[]',
        ]);

        return response()->json($process, 201);
    }

    /**
     * Muestra un proceso específico con sus documentos.
     */
    public function show(Process $process)
    {
        return response()->json($process->load('documents'));
    }

    /**
     * Actualiza un proceso existente.
     */
    public function update(Request $request, Process $process)
    {
        $validatedData = $request->validate([
            'bombero_nombre' => 'sometimes|required|string|max:255',
            'compania' => 'sometimes|required|string|max:255',
            'estado' => 'sometimes|required|string',
        ]);

        $process->update($validatedData);

        return response()->json($process);
    }

    /**
     * Elimina el proceso.
     */
    public function destroy(Process $process)
    {
        try {
        $process->delete();
        return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el proceso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el registro.'], 500);
        }
    }


    public function completeStep(Request $request, Process $process)
    {
        $validatedData = $request->validate([
            'step_title' => 'required|string|max:255',
        ]);

        $sectionsData = $process->sections_data ?? ['optional_steps_completed' => []];
        
        if (!isset($sectionsData['optional_steps_completed'])) {
            $sectionsData['optional_steps_completed'] = [];
        }

        $stepTitle = $validatedData['step_title'];

        if (!in_array($stepTitle, $sectionsData['optional_steps_completed'])) {
            $sectionsData['optional_steps_completed'][] = $stepTitle;
            
            $process->sections_data = $sectionsData;
            $process->save();
        }

        return response()->json($process->load('documents'));
    }
}