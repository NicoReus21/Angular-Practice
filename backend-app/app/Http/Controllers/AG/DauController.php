<?php

namespace App\Http\Controllers\AG;

use Illuminate\Http\Request;
use App\Http\Controllers\DocumentController;
use App\Models\Process;
class DauController extends DocumentController
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
    $document = $this->upload($process,$file,'antecedente_general','dau',$request->user()->id);
    if($document){
        return response()->json([
            'success' => true,
            'message' => 'DAU subido correctamente',
            'document' => $document,
        ], 201);
    }
    return response()->json(['success' => false, 'message' => 'El archivo no es válido.'], 400);
}
}
