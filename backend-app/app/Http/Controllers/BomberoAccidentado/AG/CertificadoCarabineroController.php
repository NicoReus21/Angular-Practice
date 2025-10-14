<?php

namespace App\Http\Controllers\BomberoAccidentado\AG;

use Illuminate\Http\Request;
use App\Models\Process;
use App\Http\Controllers\DocumentController;
class CertificadoCarabineroController extends DocumentController
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
        $document = $this->upload($process, $file, 'antecedente_general', 'certificado_carabinero', $request->user()->id);

        if ($document) {
            return response()->json([
                'success' => true,
                'message' => 'Certificado Carabinero subido correctamente',
                'document' => $document,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'El archivo no es válido.'], 400);
    }
}
