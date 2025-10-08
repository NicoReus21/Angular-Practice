<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Process;


class DocumentController extends Controller
{

    public function upload(Process $process,UploadedFile $file,$step,$section_title,int $user_id):Document{
        if ($file && $file->isValid()) {
             $extension = $file->getClientOriginalExtension();

        // Calcula incremental basado en documentos previos del mismo proceso
            $count = Document::where('process_id', $process->id)->count() + 1;

        // Genera nombre único: procesoID_incremental_fechaHora.ext
            $filename = "{$process->id}_{$count}_" . now()->format('Ymd_His') . ".{$extension}";

        // Guarda el archivo en carpeta privada reportes_flash usando el disk 'local'
            $path = $file->storeAs("private", $filename, 'local');

        // Guarda en base de datos
            $document = Document::create([
            'process_id' => $process->id,
            'user_id' => $user_id,
            'file_name' => $filename,
            'file_path' => $path,
            'section_title' => $section_title,
            'step' =>"requerimiento_operativo",
            'uploaded_at' => now(),
        ]);

        return $document;
        }
        return null;
    }
    public function destroy(Document $document)
    {
        try {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            Log::error("Error al eliminar el documento ID {$document->id}: " . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el archivo en el servidor'], 500);
        }
    }
}