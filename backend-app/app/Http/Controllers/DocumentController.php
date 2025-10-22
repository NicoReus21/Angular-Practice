<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Process;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $process = $document->process;
        $foreignKeyColumn = str_replace([' ', '(', ')', '.'], '_', strtolower($document->section_title)) . '_id';

        if ($process && Schema::hasColumn('processes', $foreignKeyColumn)) {
            $process->{$foreignKeyColumn} = null;
            $process->save();
        }

        Storage::delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Archivo eliminado correctamente.'], 200);

    } catch (\Exception $e) {
        Log::error("Error al eliminar el documento ID {$document->id}: " . $e->getMessage());
        return response()->json(['message' => 'Error al eliminar el archivo en el servidor'], 500);
    }
    }


    public function view(Document $document): Response|StreamedResponse
    {

        if (auth()->id() !== $document->user_id) {
            abort(403, 'No autorizado.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado.');
        }
        return Storage::disk('local')->response($document->file_path);
    }

    /**
     * Fuerza la descarga de un archivo privado.
     */
    public function download(Document $document): StreamedResponse
    {
        if (auth()->id() !== $document->user_id) {
            abort(403, 'No autorizado.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado.');
        }
        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }
}

