<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarDocument;
use App\Models\Company;
use App\Traits\ChecksCompanyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CarDocumentController extends Controller
{
    use ChecksCompanyPermission;
    /**
     * Almacena un nuevo documento (y gasto) para un carro.
     * POST /api/cars/{car}/documents
     */
    public function store(Request $request, Car $car)
    {
        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'create', 'Document'))) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'cost' => 'required|numeric|min:0',
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileType = $this->getFileType($file->getClientMimeType());
        $path = $file->store('documents', 'public');

        if (!$path) {
            return response()->json(['message' => 'Error al guardar el archivo.'], 500);
        }

        $document = $car->documents()->create([
            'cost' => $request->input('cost'),
            'file_name' => $originalName,
            'path' => $path, 
            'file_type' => $fileType,
            'is_paid' => false, 
        ]);

        return response()->json($document, 201);
    }

    /**
     * Alterna el estado de pago de un documento.
     * PATCH /api/documents/{document}/toggle-payment
     */
    public function togglePayment(CarDocument $document)
    {
        $company = $this->ensureCarCompany($document->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'update', 'Document'))) {
            return $response;
        }

        $document->is_paid = !$document->is_paid;
        $document->save();

        return response()->json($document);
    }

    /**
     * Elimina un documento.
     * DELETE /api/documents/{document}
     */
    public function destroy(CarDocument $document)
    {
        try {
            $company = $this->ensureCarCompany($document->car);
            if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'delete', 'Document'))) {
                return $response;
            }

            if ($document->path && Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }
            
            $document->delete();
            return response()->json(null, 204); 

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el documento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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

    private function ensureCarCompany(Car $car): ?Company
    {
        if ($car->company_id) {
            return Company::find($car->company_id);
        }

        if ($car->company) {
            $company = Company::firstOrCreate(
                ['name' => $car->company],
                ['code' => Str::slug($car->company, '-')]
            );
            if (!$company->code) {
                $company->update(['code' => Str::slug($car->company, '-')]);
            }
            $car->company_id = $company->id;
            $car->company = $company->name;
            $car->save();
            return $company;
        }

        return null;
    }
}
