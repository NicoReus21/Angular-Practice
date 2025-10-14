<?php

namespace App\Http\Controllers\BomberoAccidentado\GV;

use Illuminate\Http\Request;
use App\Models\Process;

use App\Http\Controllers\DocumentController;
class BoletaGastoAcompananteController extends DocumentController
{
    public function store(Request $request, Process $process)
    {
        // Verifica autenticación con Sanctum
        if (!$request->user()) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        // Verifica que venga un archivo en la petición
        if (!$request->hasFile('document')) {
            return response()->json(['success' => false, 'message' => 'No se recibió ningún archivo.'], 400);
        }

        $file = $request->file('document');
        $document = $this->upload($process, $file, 'otro', 'boleta_gasto_acompanante', $request->user()->id);

        if ($document) {
            return response()->json([
                'success' => true,
                'message' => 'Boleta de Gasto Acompañante subida correctamente',
                'document' => $document,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'El archivo no es válido.'], 400);
    }
}
