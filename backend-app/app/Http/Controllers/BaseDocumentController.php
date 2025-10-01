<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class BaseDocumentController extends Controller
{
    abstract protected function getSectionTitle(): string;
    abstract protected function getStepTitle(): string;
    
    public function store(Request $request, $processId)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            $file = $request->file('file');
            $path = $file->store('documents/' . $processId, 'public');

            $document = Document::create([
                'process_id' => $processId,
                'section_title' => $this->getSectionTitle(),
                'step_title' => $this->getStepTitle(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
            ]);

            return response()->json($document, 201);
        } catch (\Exception $e) {
            Log::error('Error al subir documento: ' . $e->getMessage());
            return response()->json(['message' => 'Error en el servidor al subir el archivo'], 500);
        }
    }
    
}