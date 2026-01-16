<?php 
namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Maintenance;
use App\Models\MaintenanceDocument;
use App\Models\Company;
use App\Traits\ChecksCompanyPermission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MaintenanceController extends Controller
{
    use ChecksCompanyPermission;
    public function store(Request $request, Car $car)
    {
        return $this->saveMaintenance($request, $car);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        return $this->saveMaintenance($request, $maintenance->car, $maintenance);
    }

    private function saveMaintenance(Request $request, Car $car, Maintenance $maintenance = null)
    {
        $status = $request->input('status', 'completed');
        $action = $maintenance ? 'update' : 'create';

        $company = $this->ensureCarCompany($car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission($request, $company, $action, 'Maintenance'))) {
            return $response;
        }

        $rules = [
            'service_date'      => 'required|date',
            'chassis_number'    => 'nullable|string|max:255',
            'mileage'           => 'nullable|integer',
            'cabin'             => 'nullable|string|max:255',
            'filter_code'       => 'nullable|string|max:255',
            'hourmeter'         => 'nullable|string|max:255',
            'warnings'          => 'nullable|string',
            'location'          => 'nullable|string|max:255',
            'service_type'      => 'nullable|string|max:255',
            'inspector_name'    => 'nullable|string|max:255',
            'reported_problem'  => 'nullable|string',
            'activities_detail' => 'nullable|string',
            'pending_work'      => 'nullable|string',
            'pending_type'      => 'nullable|string',
            'observations'      => 'nullable|string',
            'car_info_annex'    => 'nullable|string',
            'inspector_signature' => 'nullable|string',
            'officer_signature'   => 'nullable|string',
        ];

        if ($status === 'completed') {
            $rules['inspector_name']      = 'required|string|max:255';
            $rules['mileage']             = 'required|integer';
            $rules['service_type']        = 'required|string|max:255';
            $rules['reported_problem']    = 'required|string';
            $rules['activities_detail']   = 'required|string';
            $rules['inspector_signature'] = 'required|string';
            $rules['officer_signature']   = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['status'] = $status;
        $data['pending_type'] = $request->input('pending_type', 'Ninguno');

        if ($maintenance) {
            $maintenance->update($data);
        } else {
            $maintenance = $car->maintenances()->create($data);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('documents', 'local');
                    $maintenance->documents()->create([
                        'file_path'           => $path,
                        'mime'                => $file->getClientMimeType(),
                        'size'                => $file->getSize(),
                        'uploaded_by_user_id' => $request->user()?->id
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("Error subiendo imagen de mantenimiento: " . $e->getMessage());
                }
            }
        }

        // --- GENERACIÇ"N DE PDF ---
        if ($status === 'completed') {
            $attachedImagesPaths = $this->buildAttachedImages($maintenance);
            $filename = $this->generatePdf($maintenance, $attachedImagesPaths);
            $maintenance->pdf_url = $filename;
            $maintenance->save();
        }

        $maintenance->load('documents');

        return response()->json($maintenance, $maintenance->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Maintenance $maintenance)
    {
        try {
            $company = $this->ensureCarCompany($maintenance->car);
            if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'delete', 'Maintenance'))) {
                return $response;
            }

            if ($maintenance->pdf_url) {
                $relativePath = ltrim($maintenance->pdf_url, '/');
                if (str_starts_with($relativePath, 'http')) {
                    $path = parse_url($relativePath, PHP_URL_PATH);
                    $relativePath = ltrim($path ?? '', '/');
                }
                if (str_starts_with($relativePath, 'storage/')) {
                    $relativePath = ltrim(substr($relativePath, strlen('storage/')), '/');
                }

                if (Storage::disk('local')->exists($relativePath)) {
                    Storage::disk('local')->delete($relativePath);
                } elseif (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }

            $docs = $maintenance->documents; 
            
            foreach($docs as $doc) {
                if (Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                } elseif (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            $maintenance->delete();

            return response()->json(['message' => 'Reporte eliminado con Ã©xito.'], 200);

        } catch (\Exception $e) {
            Log::error('Error al eliminar maintenance ID ' . $maintenance->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el reporte: ' . $e->getMessage()], 500);
        }
    }

    public function downloadPdf(Maintenance $maintenance)
    {
        $company = $this->ensureCarCompany($maintenance->car);
        if ($company && ($response = $this->forbidIfNoCompanyPermission(request(), $company, 'read', 'Maintenance'))) {
            return $response;
        }

        if (!$maintenance->pdf_url) {
            return response()->json(['message' => 'PDF no disponible'], 404);
        }

        $relativePath = ltrim($maintenance->pdf_url, '/');
        if (str_starts_with($relativePath, 'http')) {
            $path = parse_url($relativePath, PHP_URL_PATH);
            $relativePath = ltrim($path ?? '', '/');
        }
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = ltrim(substr($relativePath, strlen('storage/')), '/');
        }

        $disk = null;
        if (Storage::disk('local')->exists($relativePath)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($relativePath)) {
            $disk = 'public';
        }

        if ($disk) {
            $latestDoc = $maintenance->documents()->latest('created_at')->first();
            if ($latestDoc) {
                $pdfMtime = Storage::disk($disk)->lastModified($relativePath);
                if ($pdfMtime < $latestDoc->created_at->timestamp) {
                    $attachedImagesPaths = $this->buildAttachedImages($maintenance);
                    $filename = $this->generatePdf($maintenance, $attachedImagesPaths);
                    $maintenance->pdf_url = $filename;
                    $maintenance->save();
                    return Storage::disk('local')->download($filename);
                }
            }

            return Storage::disk($disk)->download($relativePath);
        }

        if ($maintenance->status === 'completed') {
            $attachedImagesPaths = $this->buildAttachedImages($maintenance);
            $filename = $this->generatePdf($maintenance, $attachedImagesPaths);
            $maintenance->pdf_url = $filename;
            $maintenance->save();
            return Storage::disk('local')->download($filename);
        }

        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }

    private function ensureCarCompany(Car $car): ?Company
    {
        if ($car->company_id) {
            return $car->company;
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

    private function buildAttachedImages(Maintenance $maintenance): array
    {
        $maintenance->load('documents');
        $existingDocs = $maintenance->documents;
        $attachedImagesPaths = [];

        foreach ($existingDocs as $doc) {
            $disk = null;
            if (Storage::disk('local')->exists($doc->file_path)) {
                $disk = 'local';
            } elseif (Storage::disk('public')->exists($doc->file_path)) {
                $disk = 'public';
            }

            if ($disk) {
                $absolutePath = Storage::disk($disk)->path($doc->file_path);
                if (file_exists($absolutePath)) {
                    $type = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
                    if ($type === 'jpg') {
                        $type = 'jpeg';
                    }
                    $mime = $doc->mime ?: ('image/' . $type);
                    $content = file_get_contents($absolutePath);
                    $base64 = 'data:' . $mime . ';base64,' . base64_encode($content);
                    $attachedImagesPaths[] = $base64;
                }
            }
        }

        return $attachedImagesPaths;
    }

    private function generatePdf(Maintenance $maintenance, array $attachedImages): string
    {
        $pdf = Pdf::loadView('pdfs.report', [
            'maintenance' => $maintenance,
            'attachedImages' => $attachedImages
        ]);

        $filename = 'reports/reporte-' . $maintenance->car_id . '-' . $maintenance->id . '-' . time() . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }
}
