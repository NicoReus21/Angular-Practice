<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CarDocumentController extends Controller
{
    /**
     * Almacena un nuevo documento (y gasto) para un carro.
     * POST /api/cars/{car}/documents
     */
    public function store(Request $request, Car $car)
    {
        $validator = Validator::make($request->all(), [
            'cost' => 'required|numeric|min:0',
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240', // 10MB Max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileType = $this->getFileType($file->getClientMimeType());
        
        // 1. Guardar el archivo en el disco
        // La ruta será 'storage/app/public/documents/...'
        $path = $file->store('public/documents');

        if (!$path) {
            return response()->json(['message' => 'Error al guardar el archivo.'], 500);
        }

        // 2. Crear el registro en la base de datos
        $document = $car->documents()->create([
            'cost' => $request->input('cost'),
            'file_name' => $originalName,
            'path' => $path, // Guardamos la ruta interna
            'file_type' => $fileType,
        ]);

        // Devolvemos el documento (que incluirá la 'url' pública gracias al accesor)
        return response()->json($document, 201);
    }

    /**
     * Elimina un documento.
     * DELETE /api/documents/{document}
     */
    public function destroy(CarDocument $document)
    {
        // (Opcional: Añadir policy para verificar permisos)
        // $this->authorize('delete', $document);

        try {
            // 1. Eliminar el archivo del disco
            Storage::delete($document->path);

            // 2. Eliminar el registro de la base de datos
            $document->delete();

            return response()->json(null, 204); // 204 = Sin Contenido (Éxito)

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el documento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper para determinar el tipo de icono en el frontend.
     */
    private function getFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'img';
        }
        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }
        if (str_contains($mimeType, 'word')) {
            return 'doc';
        }
        return 'other';
    }
}