<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/process', [ProcessController::class, 'process']);
Route::get('/process', [ProcessController::class, 'index']);
Route::get('/process/{id}', [ProcessController::class, 'show']);
Route::put('/process/{id}', [ProcessController::class, 'update']);
Route::delete('/process/{id}', [ProcessController::class, 'destroy']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Requerimiento Operativo
Route::post('process/{id}/upload_reporte_flash', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_diab', [ProcessController::class, 'upload.diab']);
Route::post('process/{id}/upload_obac', [ProcessController::class, 'upload.obac']);
Route::post('process/{id}/upload_incidente_sin_lesiones', [ProcessController::class, 'upload.reporte_flash']);
//Antecedentes Generales
Route::post('process/{id}/upload_certificado_carabineros', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_informe_medico', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_otros_documento_medico_adicional', [ProcessController::class, 'upload.reporte_flash']);
//Documento del Cuerpo de Bomberos
Route::post('process/{id}/upload_certificado_acreditacion_voluntario', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_copia_libro_guardia', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_copia_libro_llamada', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_aviso_citacion', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_copia_lista_asistencia', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_informe_ejecutivo', [ProcessController::class, 'upload.reporte_flash']);
//Prestaciones Medica
Route::post('process/{id}/upload_factura_prestaciones', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_boleta_honorarios_visada', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_boleta_medicamentos', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_certificado_medico_autorizacion_examen', [ProcessController::class, 'upload.reporte_flash']);
//Gastos de Traslados y alimentacion
Route::post('process/{id}/upload_boleta_factura_traslado', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_certificado_medico_atencion_especial', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_certificado_medico_traslado', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_boleta_gastos_acompanante', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_boleta_alimentacion_acompanante', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_gastos_otros', [ProcessController::class, 'upload.reporte_flash']);