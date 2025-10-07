<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

class ReporteFlashController extends BaseDocumentController
{
public function upload(Request $request)
{
    // Verifica autenticación con Sanctum
    if (!$request->user()) {
        return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
    }

    $file = $request->file('document');
    $procesoId = $request->input('process_id'); // Debe venir en el request

    if ($file && $file->isValid()) {
        $extension = $file->getClientOriginalExtension();

        // Calcula incremental basado en documentos previos del mismo proceso
        $count = Document::where('proceso_id', $procesoId)->count() + 1;

        // Genera nombre único: procesoID_incremental_fechaHora.ext
        $filename = "{$procesoId}_{$count}_" . now()->format('Ymd_His') . ".{$extension}";

        // Guarda el archivo con ese nombre
        $path = $file->storeAs('reportes_flash', $filename);

        // Guarda en base de datos usando el modelo Document
        $document = Document::create([
            'process_id' => $procesoId,
            'user_id' => $request->user()->id,
            'filename' => $filename,
            'path' => $path,
            'uploaded_at' => now(),
        ]);

        // TODO: Reemplazar por clase especializada (por ejemplo ReporteFlashDocument)
        //       con validaciones y estructura más específica para reportes flash.

        return response()->json([
            'success' => true,
            'path' => $path,
            'document' => $document,
        ], 201);
    }


    return response()->json(['success' => false, 'message' => 'No se pudo subir el documento.'], 400);
}
    protected function getSectionTitle(): string
    {
        return 'REQUERIMIENTO OPERATIVO';
    }

    protected function getStepTitle(): string
    {
        return 'Reporte Flash';
    }
}
