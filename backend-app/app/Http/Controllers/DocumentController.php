<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Process;
use Illuminate\Support\Facades\Schema;

class DocumentController extends Controller
{

    public function upload(Process $process, UploadedFile $file, $step, $section_title, int $user_id): ?Document
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        try {
            $extension = $file->getClientOriginalExtension();
            $count = Document::where('process_id', $process->id)->count() + 1;
            $filename = "{$process->id}_{$count}_" . now()->format('Ymd_His') . ".{$extension}";

            $path = $file->storeAs("private", $filename, 'local');
            if ($path === false) {
                throw new \Exception("Falló el almacenamiento del archivo. Revisa los permisos de la carpeta 'storage/app/private'.");
            }

            $document = Document::create([
                'process_id' => $process->id,
                'user_id' => $user_id,
                'file_name' => $filename,
                'file_path' => $path,
                'section_title' => $section_title,
                'step' => $step,
            ]);

            $foreignKeyColumn = str_replace([' ', '(', ')', '.'], '_', strtolower($section_title)) . '_id';
            
            if (Schema::hasColumn('processes', $foreignKeyColumn)) {
                $process->{$foreignKeyColumn} = $document->id;
                $process->save();
            }

            return $document;

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error("Error de base de datos al subir documento para el proceso {$process->id}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error de base de datos. Revisa que el modelo y la migración coincidan.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error("Error general al subir documento para el proceso {$process->id}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error en el servidor al intentar guardar el archivo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Document $document)
    {
        try {
            Storage::delete($document->file_path);
            $document->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error al eliminar el documento ID {$document->id}: " . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el archivo en el servidor'], 500);
        }
    }
}

