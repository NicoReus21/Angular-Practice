<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Maintenance;
use App\Models\CarDocument; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
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

        $attachedImagesPaths = [];
        $existingDocs = CarDocument::where('maintenance_id', $maintenance->id)->get();
        
        foreach ($existingDocs as $doc) {
             $absolutePath = Storage::disk('public')->path($doc->path);
             if (file_exists($absolutePath)) {
                $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
                $content = file_get_contents($absolutePath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
                $attachedImagesPaths[] = $base64;
             }
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('documents', 'public');
                    
                    $car->documents()->create([
                        'file_name'      => $file->getClientOriginalName(),
                        'path'           => $path,
                        'file_type'      => 'img',
                        'cost'           => 0,
                        'is_paid'        => false,
                        'maintenance_id' => $maintenance->id
                    ]);
                    
                    $absolutePath = Storage::disk('public')->path($path);
                    if (file_exists($absolutePath)) {
                        $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
                        $content = file_get_contents($absolutePath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);
                        $attachedImagesPaths[] = $base64;
                    }

                } catch (\Exception $e) {
                    Log::error("Error subiendo imagen: " . $e->getMessage());
                }
            }
        }

        if ($status === 'completed') {
            $pdf = Pdf::loadView('pdfs.report', [
                'maintenance' => $maintenance,
                'attachedImages' => $attachedImagesPaths
            ]);
            
            $filename = 'reports/reporte-' . $car->id . '-' . $maintenance->id . '-' . time() . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());

            $maintenance->pdf_url = Storage::url($filename);
            $maintenance->save();
        }

        return response()->json($maintenance, $maintenance->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Maintenance $maintenance)
    {
        try {
            if ($maintenance->pdf_url) {
                $relativePath = str_replace('/storage/', '', parse_url($maintenance->pdf_url, PHP_URL_PATH));

                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
            $docs = CarDocument::where('maintenance_id', $maintenance->id)->get();
            
            foreach($docs as $doc) {
                if (Storage::disk('public')->exists($doc->path)) {
                    Storage::disk('public')->delete($doc->path);
                }
                $doc->delete();
            }

            $maintenance->delete();

            return response()->json(['message' => 'Reporte eliminado con éxito.'], 200);

        } catch (\Exception $e) {
            Log::error('Error al eliminar maintenance ID ' . $maintenance->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el reporte: ' . $e->getMessage()], 500);
        }
    }
}