<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ReporteFlashController;
use App\Http\Controllers\DiabController;
use App\Http\Controllers\ObacController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CertificadoCarabineroController;
use App\Http\Controllers\InformeMedicoController;
use App\Http\Controllers\OtroDocumentoMedicoController;
use App\Http\Controllers\CertificadoAcreditacionVoluntarioController;
use App\Http\Controllers\CopiaLibroGuardiaController;
use App\Http\Controllers\CopiaLibroLlamadaController;
use App\Http\Controllers\AvisoCitacionController;
use App\Http\Controllers\CopiaListaAsistenciaController;
use App\Http\Controllers\InformeEjecutivoController;
use App\Http\Controllers\FacturaPrestacionController;
use App\Http\Controllers\BoletaHonorarioVisadaController;
use App\Http\Controllers\BoletaMedicamentoController;
use App\Http\Controllers\CertificadoMedicoAutorizacionExamenController;
use App\Http\Controllers\BoletaFacturaController;
use App\Http\Controllers\CertificadoMedicoAtencionEspecialController;
use App\Http\Controllers\CertificadoMedicoTrasladoController;
use App\Http\Controllers\BoletaGastoAcompananteController;
use App\Http\Controllers\OtroGastoController;

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
Route::post('process/{id}/upload_reporte_flash', [ReporteFlashController::class, 'upload'])->name('upload.reporte_flash');
Route::post('process/{id}/upload_diab', [DiabController::class, 'upload'])->name('upload.diab');
Route::post('process/{id}/upload_obac', [ObacController::class, 'upload'])->name('upload.obac');
//TODO: cambiar Controlador al correcto
Route::post('process/{id}/upload_incidente_sin_lesiones', [ReporteController::class, 'upload.reporte_flash']);
//Antecedentes Generales
Route::post('process/{id}/upload_certificado_carabineros', [CertificadoCarabineroController::class, 'upload'])->name('upload.certificado_carabineros');
Route::post('process/{id}/upload_informe_medico', [InformeMedicoController::class, 'upload'])->name('upload.informe_medico');
Route::post('process/{id}/upload_otros_documento_medico_adicional', [OtroDocumentoMedicoController::class, 'upload'])->name('upload.otros_documento_medico_adicional');
//Documento del Cuerpo de Bomberos
Route::post('process/{id}/upload_certificado_acreditacion_voluntario', [CertificadoAcreditacionVoluntarioController::class, 'upload'])->name('upload.certificado_acreditacion_voluntario')  ;
Route::post('process/{id}/upload_copia_libro_guardia', [CopiaLibroGuardiaController::class, 'upload'])->name('upload.copia_libro_guardia');
Route::post('process/{id}/upload_copia_libro_llamada', [CopiaLibroLlamadaController::class, 'upload'])->name('upload.copia_libro_llamada');
Route::post('process/{id}/upload_aviso_citacion', [AvisoCitacionController::class, 'upload'])->name('upload.aviso_citacion');
Route::post('process/{id}/upload_copia_lista_asistencia', [CopiaListaAsistenciaController::class, 'upload'])->name('upload.copia_lista_asistencia');
Route::post('process/{id}/upload_informe_ejecutivo', [InformeEjecutivoController::class, 'upload'])->name('upload.informe_ejecutivo');
//Prestaciones Medica
Route::post('process/{id}/upload_factura_prestaciones', [FacturaPrestacionController::class, 'upload'])->name('upload.factura_prestaciones');
Route::post('process/{id}/upload_boleta_honorarios_visada', [BoletaHonorarioVisadaController::class, 'upload'])->name('upload.boleta_honorarios_visada');
Route::post('process/{id}/upload_boleta_medicamentos', [BoletaMedicamentoController::class, 'upload'])->name('upload.boleta_medicamentos');
Route::post('process/{id}/upload_certificado_medico_autorizacion_examen', [CertificadoMedicoAutorizacionExamenController::class, 'upload'])->name('upload.certificado_medico_autorizacion_examen');
//Gastos de Traslados y alimentacion
Route::post('process/{id}/upload_boleta_factura_traslado', [BoletaFacturaController::class, 'upload'])->name('upload.boleta_factura_traslado');
Route::post('process/{id}/upload_certificado_medico_atencion_especial', [CertificadoMedicoAtencionEspecialController::class, 'upload'])->name('upload.certificado_medico_atencion_especial');
Route::post('process/{id}/upload_certificado_medico_traslado', [CertificadoMedicoTrasladoController::class, 'upload'])->name('upload.certificado_medico_traslado');
Route::post('process/{id}/upload_boleta_gastos_acompanante', [BoletaGastoAcompananteController::class, 'upload'])->name('upload.boleta_gastos_acompanante');
//TODO: cambiar controlador al correcto
Route::post('process/{id}/upload_boleta_alimentacion_acompanante', [ProcessController::class, 'upload.reporte_flash']);
Route::post('process/{id}/upload_gastos_otros', [OtroGastoController::class, 'upload.reporte_flash']);


// Mini sistema de usuarios y grupos
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'index']);
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/groups', [AuthController::class, 'index']);
    Route::get('/groups/{id}', [AuthController::class, 'show']);
    Route::post('/groups', [AuthController::class, 'store']);
    Route::put('/groups/{id}', [AuthController::class, 'update']);
    Route::delete('/groups/{id}', [AuthController::class, 'destroy']);
});