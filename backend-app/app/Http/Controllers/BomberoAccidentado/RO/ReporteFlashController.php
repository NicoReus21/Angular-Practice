<?php

namespace App\Http\Controllers\BomberoAccidentado\RO;

use Illuminate\Http\Request;
use App\Models\Process;
use App\Http\Controllers\DocumentController;

/**
 * @OA\Tag(
 *     name="ReporteFlash",
 *     description="Endpoints relacionados con la subida de Reportes Flash"
 * )
 */
class ReporteFlashController extends DocumentController
{
    /**
     * @OA\Post(
     *     path="/processes/{process}/reporte-flash",
     *     tags={"ReporteFlash"},
     *     summary="Sube un archivo de Reporte Flash para un proceso específico",
     *     description="Permite subir un archivo PDF o imagen asociado a un proceso. Requiere autenticación con Sanctum.",
     *     operationId="uploadReporteFlash",
     *     
     *     @OA\Parameter(
     *         name="process",
     *         in="path",
     *         required=true,
     *         description="ID del proceso al cual se le asociará el reporte",
     *         @OA\Schema(type="integer")
     *     ),
     *     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Archivo del reporte (PDF, JPG, PNG)",
     *                     property="document",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=201,
     *         description="Reporte Flash subido correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Reporte Flash subido correctamente"),
     *             @OA\Property(
     *                 property="document",
     *                 type="object",
     *                 @OA\Property(property="process_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="file_name", type="string", example="1_1_20251008_012825.pdf"),
     *                 @OA\Property(property="file_path", type="string", example="private/1_1_20251008_012825.pdf"),
     *                 @OA\Property(property="section_title", type="string", example="reporte_flash"),
     *                 @OA\Property(property="step", type="string", example="requerimiento_operativo"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-08T01:28:25Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-08T01:28:25Z"),
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=400,
     *         description="No se recibió archivo o archivo inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El archivo no es válido.")
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No autenticado.")
     *         )
     *     ),
     *     
     *     security={{"sanctum": {}}}
     * )
     */
    public function store(Request $request, Process $process)
    {
        // Verifica autenticación con Sanctum
        if (!$request->user()) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        // Verifica que venga un archivo en la petición
        if (!$request->hasFile('document')) {
            return response()->json(['success' => false, 'message' => 'No se recibió ningún archivo.'], 400);
        }

        $file = $request->file('document');
        $document = $this->upload($process, $file, 'requerimiento_operativo', 'reporte_flash', $request->user()->id);

        if ($document) {
            return response()->json([
                'success' => true,
                'message' => 'Reporte Flash subido correctamente',
                'document' => $document,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'El archivo no es válido.'], 400);
    }
}