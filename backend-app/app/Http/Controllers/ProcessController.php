<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProcessController extends Controller
{
    /**
     * Lista todos los procesos con sus documentos.
     */
    public function index()
    {
        $processes = Process::with('documents')
            ->orderBy('created_at', 'desc')
            ->get();

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

        $userId = $request->user()->id;

        $process = Process::create([
            'bombero_name' => $validatedData['bombero_name'],
            'company' => $validatedData['company'],
            'user_id' => $userId,
            'status' => 'Iniciado',
        ]);

        return response()->json($process, 201);
    }

    /**
     * Muestra un proceso especifico con sus documentos.
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
            'bombero_name' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|required|string|max:255',
            'status' => ['sometimes', 'required', Rule::in(['Iniciado', 'Finalizado'])],
        ]);

        $process->update($validatedData);

        return response()->json($process);
    }

    /**
     * Finaliza el proceso de documentación.
     */
    public function finalize(Process $process)
    {
        try {
            if ($process->status === 'Finalizado') {
                return response()->json([
                    'message' => 'El proceso ya está finalizado.',
                    'process' => $process,
                ]);
            }

            $process->status = 'Finalizado';
            $process->save();

            return response()->json([
                'message' => 'Documentación finalizada correctamente',
                'process' => $process,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al finalizar el proceso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al finalizar el proceso'], 500);
        }
    }

    /**
     * Marca un paso como completado (usa el mismo estado Finalizado por ahora).
     */
    public function completeStep(Request $request, Process $process)
    {
        try {
            if ($process->status === 'Finalizado') {
                return response()->json([
                    'message' => 'El proceso ya está finalizado.',
                    'process' => $process,
                ]);
            }

            $process->status = 'Finalizado';
            $process->save();

            return response()->json([
                'message' => 'Paso completado y proceso finalizado.',
                'process' => $process,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al completar paso del proceso: ' . $e->getMessage());
            return response()->json(['message' => 'Error al completar el paso'], 500);
        }
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
}
