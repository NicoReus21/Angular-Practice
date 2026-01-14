<?php

namespace App\Http\Controllers;

use App\Mail\VendorReportLinkMail;
use App\Models\Car;
use App\Models\Maintenance;
use App\Models\Vendor;
use App\Models\VendorReportLink;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorReportLinkController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'car_id' => 'required|exists:cars,id',
            'expires_in_days' => 'required|integer|min:1|max:365',
        ]);

        $vendor = Vendor::updateOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'] ?? null]
        );

        $token = $this->generateToken();
        $expiresAt = Carbon::now()->addDays($data['expires_in_days']);

        $link = VendorReportLink::create([
            'vendor_id' => $vendor->id,
            'car_id' => $data['car_id'],
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_by_user_id' => $request->user()?->id,
        ]);

        $frontendUrl = config('app.frontend_url');
        $url = rtrim($frontendUrl, '/') . '/vendor-report/' . $token;
        $car = Car::find($data['car_id']);

        Mail::to($vendor->email)->send(new VendorReportLinkMail($vendor, $car, $url, $expiresAt));

        return response()->json([
            'token' => $token,
            'expires_at' => $expiresAt->toISOString(),
            'url' => $url,
        ], 201);
    }

    public function show(string $token)
    {
        $link = VendorReportLink::with(['vendor', 'car'])->where('token', $token)->first();

        if (!$link) {
            return response()->json(['message' => 'Link no encontrado'], 404);
        }

        if ($link->used_at) {
            return response()->json(['message' => 'Link ya utilizado'], 410);
        }

        if ($link->expires_at->isPast()) {
            return response()->json(['message' => 'Link expirado'], 410);
        }

        return response()->json([
            'vendor' => [
                'name' => $link->vendor?->name,
                'email' => $link->vendor?->email,
            ],
            'car' => $link->car,
            'expires_at' => $link->expires_at->toISOString(),
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $link = VendorReportLink::with(['vendor', 'car'])->where('token', $token)->first();

        if (!$link) {
            return response()->json(['message' => 'Link no encontrado'], 404);
        }

        if ($link->used_at) {
            return response()->json(['message' => 'Link ya utilizado'], 410);
        }

        if ($link->expires_at->isPast()) {
            return response()->json(['message' => 'Link expirado'], 410);
        }

        $data = $request->validate([
            'service_date' => 'required|date',
            'mileage' => 'required|integer',
            'service_type' => 'required|string|max:255',
            'inspector_name' => 'required|string|max:255',
            'reported_problem' => 'required|string',
            'activities_detail' => 'required|string',
            'inspector_signature' => 'required|string',
            'officer_signature' => 'required|string',
            'chassis_number' => 'nullable|string|max:255',
            'cabin' => 'nullable|string|max:255',
            'filter_code' => 'nullable|string|max:255',
            'hourmeter' => 'nullable|string|max:255',
            'warnings' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'pending_work' => 'nullable|string',
            'pending_type' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'car_info_annex' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|image|max:5120',
        ]);

        $data['status'] = 'completed';
        $data['car_id'] = $link->car_id;
        $data['vendor_id'] = $link->vendor_id;
        $data['pending_type'] = $data['pending_type'] ?? 'Ninguno';

        $maintenance = Maintenance::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('documents', 'local');
                    $maintenance->documents()->create([
                        'file_path' => $path,
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_by_user_id' => null,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error subiendo imagen de mantenimiento (vendor): ' . $e->getMessage());
                }
            }
        }

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

        $pdf = Pdf::loadView('pdfs.report', [
            'maintenance' => $maintenance,
            'attachedImages' => $attachedImagesPaths
        ]);

        $filename = 'reports/reporte-' . $link->car_id . '-' . $maintenance->id . '-' . time() . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        $maintenance->pdf_url = $filename;
        $maintenance->save();

        $link->update([
            'used_at' => Carbon::now(),
            'maintenance_id' => $maintenance->id,
        ]);

        return response()->json([
            'message' => 'Reporte registrado',
            'maintenance_id' => $maintenance->id,
        ], 201);
    }

    private function generateToken(): string
    {
        do {
            $token = Str::random(48);
        } while (VendorReportLink::where('token', $token)->exists());

        return $token;
    }
}
