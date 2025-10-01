<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DocumentController extends Controller
{
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