<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarDocument;
use Illuminate\Http\Request;

class CarDocumentController extends Controller
{
    public function index(Car $car)
    {
        return $car->documents()->latest()->get();
    }

    public function store(Request $request, Car $car)
    {
        $data = $request->validate([
            'type' => 'required|string|max:100',
            'file_path' => 'required|string',
            'issue_date' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);
        $data['uploaded_by_user_id'] = $request->user()?->id;
        $doc = $car->documents()->create($data);
        return response()->json($doc, 201);
    }

    public function show(CarDocument $document)
    {
        return $document;
    }

    public function update(Request $request, CarDocument $document)
    {
        $data = $request->validate([
            'type' => 'sometimes|required|string|max:100',
            'file_path' => 'sometimes|required|string',
            'issue_date' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'alert_sent_at' => 'nullable|date',
        ]);
        $document->update($data);
        return $document;
    }

    public function destroy(CarDocument $document)
    {
        $document->delete();
        return response()->noContent();
    }
}

