<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\DocumentController;
// Requerimiento Operativo
use App\Http\Controllers\BomberoAccidentado\RO\ReporteFlashController;
use App\Http\Controllers\BomberoAccidentado\RO\DiabController;
use App\Http\Controllers\BomberoAccidentado\RO\ObacController;
use App\Http\Controllers\BomberoAccidentado\RO\DeclaracionTestigoController;
use App\Http\Controllers\BomberoAccidentado\RO\CopiaLibroGuardiaController;
// Antecedentes Generales
use App\Http\Controllers\BomberoAccidentado\AG\CertificadoCarabineroController;
use App\Http\Controllers\BomberoAccidentado\AG\DauController;
use App\Http\Controllers\BomberoAccidentado\AG\CertificadoMedicoAtencionEspecialController;
use App\Http\Controllers\BomberoAccidentado\AG\InformeMedicoController;
use App\Http\Controllers\BomberoAccidentado\AG\OtroDocumentoMedicoController;
// Documentos del Cuerpo de Bomberos
use App\Http\Controllers\BomberoAccidentado\CB\CertificadoAcreditacionVoluntarioController;
use App\Http\Controllers\BomberoAccidentado\CB\CopiaLibroLlamadaController;
use App\Http\Controllers\BomberoAccidentado\CB\AvisoCitacionController;
use App\Http\Controllers\BomberoAccidentado\CB\CopiaListaAsistenciaController;
use App\Http\Controllers\BomberoAccidentado\CB\InformeEjecutivoController;
// Prestaciones Médicas
use App\Http\Controllers\BomberoAccidentado\PM\FacturaPrestacionController;
use App\Http\Controllers\BomberoAccidentado\PM\BoletaHonorarioVisadaController;
use App\Http\Controllers\BomberoAccidentado\PM\BoletaMedicamentoController;
use App\Http\Controllers\BomberoAccidentado\PM\CertificadoMedicoAutorizacionExamenController;
// Gastos de Traslados y Alimentación
use App\Http\Controllers\BomberoAccidentado\GV\BoletaFacturaTrasladoController;
use App\Http\Controllers\BomberoAccidentado\GV\CertificadoMedicoTrasladoController;
use App\Http\Controllers\BomberoAccidentado\GV\BoletaGastoAcompananteController;
use App\Http\Controllers\BomberoAccidentado\GV\OtroGastoController;
use App\Http\Controllers\BomberoAccidentado\GV\CerfiticadoMedicoIncapacidadController;

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

// Rutas Modulo material Mayor

//TODO: Agregar las rutas del modulo de Material Mayor aqui
Route::middleware('auth:sanctum')->group(function () {
    // Companies
    Route::apiResource('companies', \App\Http\Controllers\CompanyController::class);

    // Suppliers
    Route::apiResource('suppliers', \App\Http\Controllers\SupplierController::class);

    // Cars (ya existe CarController en el proyecto)
    Route::apiResource('cars', \App\Http\Controllers\CarController::class);

    // Maintenances
    Route::apiResource('maintenances', \App\Http\Controllers\MaintenanceController::class);

    // Car Documents (nested under cars) con rutas superficiales para show/update/destroy
    Route::apiResource('cars.documents', \App\Http\Controllers\CarDocumentController::class)->shallow();

    // Maintenance Documents (nested under maintenances)
    Route::apiResource('maintenances.documents', \App\Http\Controllers\MaintenanceDocumentController::class)->shallow();

    // Purchase Orders y sus Items
    Route::apiResource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);
    Route::apiResource('purchase-orders.items', \App\Http\Controllers\PoItemController::class)->shallow();

    // Invoices y Payments del módulo Material Mayor
    Route::apiResource('mm-invoices', \App\Http\Controllers\InvoicesMmController::class);
    Route::apiResource('mm-payments', \App\Http\Controllers\PaymentsMmController::class);

    // Budgets por compañía y periodo
    Route::apiResource('budgets', \App\Http\Controllers\BudgetController::class);
});
