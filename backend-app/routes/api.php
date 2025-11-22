<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserRolController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CarDocumentController;
use App\Http\Controllers\CarChecklistController;
use App\Http\Controllers\BomberoAccidentado\RO\ReporteFlashController;
use App\Http\Controllers\BomberoAccidentado\RO\DiabController;
use App\Http\Controllers\BomberoAccidentado\RO\ObacController;
use App\Http\Controllers\BomberoAccidentado\RO\CopiaLibroGuardiaController;
use App\Http\Controllers\BomberoAccidentado\RO\DeclaracionTestigoController;
use App\Http\Controllers\BomberoAccidentado\AG\CertificadoCarabineroController;
use App\Http\Controllers\BomberoAccidentado\AG\DauController;
use App\Http\Controllers\BomberoAccidentado\AG\InformeMedicoController;
use App\Http\Controllers\BomberoAccidentado\AG\OtroDocumentoMedicoController;
use App\Http\Controllers\BomberoAccidentado\AG\CertificadoMedicoAtencionEspecialController;
use App\Http\Controllers\BomberoAccidentado\CB\CertificadoAcreditacionVoluntarioController;
use App\Http\Controllers\BomberoAccidentado\CB\CopiaLibroLlamadaController;
use App\Http\Controllers\BomberoAccidentado\CB\AvisoCitacionController;
use App\Http\Controllers\BomberoAccidentado\CB\CopiaListaAsistenciaController;
use App\Http\Controllers\BomberoAccidentado\CB\InformeEjecutivoController;
use App\Http\Controllers\BomberoAccidentado\PM\FacturaPrestacionController;
use App\Http\Controllers\BomberoAccidentado\PM\BoletaHonorarioVisadaController;
use App\Http\Controllers\BomberoAccidentado\PM\BoletaMedicamentoController;
use App\Http\Controllers\BomberoAccidentado\PM\CertificadoMedicoAutorizacionExamenController;
use App\Http\Controllers\BomberoAccidentado\GV\BoletaFacturaTrasladoController;
use App\Http\Controllers\BomberoAccidentado\GV\CertificadoMedicoTrasladoController;
use App\Http\Controllers\BomberoAccidentado\GV\BoletaGastoAcompananteController;
use App\Http\Controllers\BomberoAccidentado\GV\CerfiticadoMedicoIncapacidadController;
use App\Http\Controllers\BomberoAccidentado\GV\OtroGastoController;
use App\Http\Controllers\CarChecklistItemsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::patch('/processes/{process}/complete-step', [ProcessController::class, 'completeStep']);

Route::middleware('auth:sanctum')->group( function(){
//crear proceso
    Route::post('/process', [ProcessController::class, 'store'])->name('process.store');
    Route::get('/process', [ProcessController::class, 'index']);  
    Route::get('/process/{process}', [ProcessController::class, 'show']); 
    Route::put('/process/{process}', [ProcessController::class, 'update']);     
    Route::delete('/process/{process}', [ProcessController::class, 'destroy']);  
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    Route::apiResource('user-rols', UserRolController::class)->only(['store', 'destroy']);

    Route::get('/users/{user}/roles', [UserRolController::class, 'getRolesForUser']);
});


Route::middleware('auth:sanctum')->prefix('process/{process}')->group(function () {

    // Requerimiento Operativo
    Route::post('/upload_reporte_flash', [ReporteFlashController::class, 'store'])->name('process.upload.reporte_flash');
    Route::post('/upload_diab', [DiabController::class, 'store'])->name('process.upload.diab');
    Route::post('/upload_obac', [ObacController::class, 'store'])->name('process.upload.obac');
    Route::post('/upload_copia_libro_guardia', [CopiaLibroGuardiaController::class, 'store'])->name('process.upload.copia_libro_guardia');
    Route::post('/upload_declaracion_testigo', [DeclaracionTestigoController::class, 'store'])->name('process.upload.declaracion_testigo');
    //Antecedentes Generales
    Route::post('/upload_certificado_carabineros', [CertificadoCarabineroController::class, 'store'])->name('process.upload.certificado_carabineros');
    Route::post('/upload_dau', [DauController::class, 'store'])->name('process.upload.dau');
    Route::post('/upload_informe_medico', [InformeMedicoController::class, 'store'])->name('process.upload.informe_medico');
    Route::post('/upload_otros_documento_medico_adicional', [OtroDocumentoMedicoController::class, 'store'])->name('process.upload.otros_documento_medico_adicional');
    Route::post('/upload_certificado_medico_atencion_especial', [CertificadoMedicoAtencionEspecialController::class, 'store'])->name('process.upload.certificado_medico_atencion_especial');
    //Documento del Cuerpo de Bomberos
    Route::post('/upload_certificado_acreditacion_voluntario', [CertificadoAcreditacionVoluntarioController::class, 'store'])->name('process.upload.certificado_acreditacion_voluntario');
    Route::post('/upload_copia_libro_llamada', [CopiaLibroLlamadaController::class, 'store'])->name('process.upload.copia_libro_llamada');
    Route::post('/upload_aviso_citacion', [AvisoCitacionController::class, 'store'])->name('process.upload.aviso_citacion');
    Route::post('/upload_copia_lista_asistencia', [CopiaListaAsistenciaController::class, 'store'])->name('process.upload.copia_lista_asistencia');
    Route::post('/upload_informe_ejecutivo', [InformeEjecutivoController::class, 'store'])->name('process.upload.informe_ejecutivo');
    //Prestaciones Medica
    Route::post('/upload_factura_prestaciones', [FacturaPrestacionController::class, 'store'])->name('process.upload.factura_prestaciones');
    Route::post('/upload_boleta_honorarios_visada', [BoletaHonorarioVisadaController::class, 'store'])->name('process.upload.boleta_honorarios_visada');
    Route::post('/upload_boleta_medicamentos', [BoletaMedicamentoController::class, 'store'])->name('process.upload.boleta_medicamentos');
    Route::post('/upload_certificado_medico_autorizacion_examen', [CertificadoMedicoAutorizacionExamenController::class, 'store'])->name('process.upload.certificado_medico_autorizacion_examen');
    //Gastos de Traslados y alimentacion
    Route::post('/upload_boleta_factura_traslado', [BoletaFacturaTrasladoController::class, 'store'])->name('process.upload.boleta_factura_traslado');
    Route::post('/upload_certificado_medico_traslado', [CertificadoMedicoTrasladoController::class, 'store'])->name('process.upload.certificado_medico_traslado');
    Route::post('/upload_boleta_gastos_acompanante', [BoletaGastoAcompananteController::class, 'store'])->name('process.upload.boleta_gastos_acompanante');
    Route::post('/upload_certificado_medico_incapacidad', [CerfiticadoMedicoIncapacidadController::class, 'store'])->name('process.upload.certificado_medico_incapacidad');
    Route::post('/upload_boleta_alimentacion_acompanante', [BoletaGastoAcompananteController::class, 'store'])->name('process.upload.boleta_alimentacion_acompanante');
    Route::post('/upload_otros_gastos', [OtroGastoController::class, 'store'])->name('process.upload.otros_gastos');
});

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
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('rols', RolController::class);
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/rols/{rol}/permissions', [RolController::class, 'getPermissions']);
    Route::post('/rols/{rol}/permissions', [RolController::class, 'syncPermissions']);
});


// === RUTAS DEL MÓDULO MATERIAL MAYOR ===
// Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cars', CarController::class);
    // --- Reportes (Mantenciones) ---
    Route::post('/cars/{car}/maintenances', [MaintenanceController::class, 'store']);
    Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy']);

    // --- Checklists ---
    Route::post('/cars/{car}/checklists', [CarChecklistController::class, 'store']);
    Route::put('/checklists/{checklist}', [CarChecklistController::class, 'update']);
    Route::delete('/checklists/{checklist}', [CarChecklistController::class, 'destroy']);
    Route::patch('/checklist-items/{item}/toggle', [CarChecklistController::class, 'toggleItem']);
    // --- Documentos (Gastos) ---
    Route::post('/cars/{car}/documents', [CarDocumentController::class, 'store']);
    Route::delete('/documents/{document}', [CarDocumentController::class, 'destroy']);
    
// });
