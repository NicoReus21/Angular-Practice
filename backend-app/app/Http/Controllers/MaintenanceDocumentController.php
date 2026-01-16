<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\MaintenanceDocument;
use App\Models\Company;
use App\Models\Car;
use App\Traits\ChecksCompanyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaintenanceDocumentController extends Controller
{
    use ChecksCompanyPermission;
    public function index(Maintenance $maintenance)
    {
        $company = $this->ensureCarCompany($maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read', 'Document'))) {
            return $response;
        }

        return $maintenance->documents()->latest()->get();
    }

    public function store(Request $request, Maintenance $maintenance)
    {
        $company = $this->ensureCarCompany($maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'create', 'Document'))) {
            return $response;
        }

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
        $company = $this->ensureCarCompany($document->maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read', 'Document'))) {
            return $response;
        }

        return $document;
    }

    public function update(Request $request, MaintenanceDocument $document)
    {
        $company = $this->ensureCarCompany($document->maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, 'update', 'Document'))) {
            return $response;
        }

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
        $company = $this->ensureCarCompany($document->maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'delete', 'Document'))) {
            return $response;
        }

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
        $company = $this->ensureCarCompany($document->maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read', 'Document'))) {
            return $response;
        }

        if (Storage::disk('local')->exists($document->file_path)) {
            return Storage::disk('local')->response($document->file_path);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->response($document->file_path);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
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
