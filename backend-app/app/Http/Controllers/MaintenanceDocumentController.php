<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\MaintenanceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceDocumentController extends Controller
{
    public function index(Maintenance $maintenance)
    {
        return $maintenance->documents()->latest()->get();
    }

    public function store(Request $request, Maintenance $maintenance)
    {
        $data = $request->validate([
            'file_path' => 'required|string',
            'mime' => 'nullable|string',
            'size' => 'nullable|integer',
        ]);
        $data['uploaded_by_user_id'] = $request->user()?->id;
        $doc = $maintenance->documents()->create($data);
        return response()->json($doc, 201);
    }

    public function show(MaintenanceDocument $document)
    {
        return $document;
    }

    public function update(Request $request, MaintenanceDocument $document)
    {
        $data = $request->validate([
            'file_path' => 'sometimes|required|string',
            'mime' => 'nullable|string',
            'size' => 'nullable|integer',
        ]);
        $document->update($data);
        return $document;
    }

    public function destroy(MaintenanceDocument $document)
    {
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        } elseif (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return response()->noContent();
    }

    public function download(MaintenanceDocument $document)
    {
        if (Storage::disk('local')->exists($document->file_path)) {
            return Storage::disk('local')->response($document->file_path);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->response($document->file_path);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }
}
