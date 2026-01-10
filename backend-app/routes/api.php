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
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\GroupPermissionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/google-login', [AuthController::class, 'googleLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/user/permissions', [UserController::class, 'permissions']);
    Route::patch('/processes/{process}/complete-step', [ProcessController::class, 'completeStep'])
        ->middleware('permission:Bombero Accidentado:Process:update');

    // Procesos
    Route::post('/process', [ProcessController::class, 'store'])
        ->middleware('permission:Bombero Accidentado:Process:create')
        ->name('process.store');
    Route::get('/process', [ProcessController::class, 'index'])
        ->middleware('permission:Bombero Accidentado:Process:read');
    Route::get('/process/{process}', [ProcessController::class, 'show'])
        ->middleware('permission:Bombero Accidentado:Process:read');
    Route::put('/process/{process}', [ProcessController::class, 'update'])
        ->middleware('permission:Bombero Accidentado:Process:update');
    Route::patch('/process/{process}/finalize', [ProcessController::class, 'finalize'])
        ->middleware('permission:Bombero Accidentado:Process:update')
        ->name('process.finalize');
    Route::delete('/process/{process}', [ProcessController::class, 'destroy'])
        ->middleware('permission:Bombero Accidentado:Process:delete');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])
        ->middleware('permission:Bombero Accidentado:Process:update|Material Mayor:Document:delete');

    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])
        ->middleware('permission:Bombero Accidentado:Process:read|Material Mayor:Document:read')
        ->name('documents.view');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->middleware('permission:Bombero Accidentado:Process:read|Material Mayor:Document:read')
        ->name('documents.download');

    Route::apiResource('user-rols', UserRolController::class)
        ->only(['store', 'destroy'])
        ->middleware('permission:Sistema:User:update');
    Route::get('/users/{user}/roles', [UserRolController::class, 'getRolesForUser'])
        ->middleware('permission:Sistema:User:read');
    Route::post('/users/{userId}/roles/{roleId}', [UserRolController::class, 'assign'])
        ->middleware('permission:Sistema:User:update');
    Route::delete('/users/{userId}/roles/{roleId}', [UserRolController::class, 'remove'])
        ->middleware('permission:Sistema:User:update');

    Route::get('/users/{user}/groups', [UserGroupController::class, 'getGroupsForUser'])
        ->middleware('permission:Sistema:User:read');
    Route::get('/groups/{group}/users', [UserGroupController::class, 'getUsersForGroup'])
        ->middleware('permission:Sistema:Group:read');
    Route::prefix('process/{process}')->group(function () {
        // Requerimiento Operativo
        Route::post('/upload_reporte_flash', [ReporteFlashController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.reporte_flash');
        Route::post('/upload_diab', [DiabController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.diab');
        Route::post('/upload_obac', [ObacController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.obac');
        Route::post('/upload_copia_libro_guardia', [CopiaLibroGuardiaController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.copia_libro_guardia');
        Route::post('/upload_declaracion_testigo', [DeclaracionTestigoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.declaracion_testigo');
        // Antecedentes Generales
        Route::post('/upload_certificado_carabineros', [CertificadoCarabineroController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.certificado_carabineros');
        Route::post('/upload_dau', [DauController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.dau');
        Route::post('/upload_informe_medico', [InformeMedicoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.informe_medico');
        Route::post('/upload_otros_documento_medico_adicional', [OtroDocumentoMedicoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.otros_documento_medico_adicional');
        Route::post('/upload_certificado_medico_atencion_especial', [CertificadoMedicoAtencionEspecialController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.certificado_medico_atencion_especial');
        // Documento del Cuerpo de Bomberos
        Route::post('/upload_certificado_acreditacion_voluntario', [CertificadoAcreditacionVoluntarioController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.certificado_acreditacion_voluntario');
        Route::post('/upload_copia_libro_llamada', [CopiaLibroLlamadaController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.copia_libro_llamada');
        Route::post('/upload_aviso_citacion', [AvisoCitacionController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.aviso_citacion');
        Route::post('/upload_copia_lista_asistencia', [CopiaListaAsistenciaController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.copia_lista_asistencia');
        Route::post('/upload_informe_ejecutivo', [InformeEjecutivoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.informe_ejecutivo');
        // Prestaciones Medica
        Route::post('/upload_factura_prestaciones', [FacturaPrestacionController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.factura_prestaciones');
        Route::post('/upload_boleta_honorarios_visada', [BoletaHonorarioVisadaController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.boleta_honorarios_visada');
        Route::post('/upload_boleta_medicamentos', [BoletaMedicamentoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.boleta_medicamentos');
        Route::post('/upload_certificado_medico_autorizacion_examen', [CertificadoMedicoAutorizacionExamenController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.certificado_medico_autorizacion_examen');
        // Gastos de Traslados y alimentacion
        Route::post('/upload_boleta_factura_traslado', [BoletaFacturaTrasladoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.boleta_factura_traslado');
        Route::post('/upload_certificado_medico_traslado', [CertificadoMedicoTrasladoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.certificado_medico_traslado');
        Route::post('/upload_boleta_gastos_acompanante', [BoletaGastoAcompananteController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.boleta_gastos_acompanante');
        Route::post('/upload_certificado_medico_incapacidad', [CerfiticadoMedicoIncapacidadController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.certificado_medico_incapacidad');
        Route::post('/upload_boleta_alimentacion_acompanante', [BoletaGastoAcompananteController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.boleta_alimentacion_acompanante');
        Route::post('/upload_otros_gastos', [OtroGastoController::class, 'store'])
            ->middleware('permission:Bombero Accidentado:Process:update')
            ->name('process.upload.otros_gastos');
    });

    // Mini sistema de usuarios y grupos
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:Sistema:User:read');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:Sistema:User:create');
    Route::get('/users/{id}', [UserController::class, 'show'])
        ->middleware('permission:Sistema:User:read');
    Route::put('/users/{id}', [UserController::class, 'update'])
        ->middleware('permission:Sistema:User:update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:Sistema:User:delete');

    Route::get('/groups', [GroupController::class, 'index'])
        ->middleware('permission:Sistema:Group:read');
    Route::get('/groups/{id}', [GroupController::class, 'show'])
        ->middleware('permission:Sistema:Group:read');
    Route::post('/groups', [GroupController::class, 'store'])
        ->middleware('permission:Sistema:Group:create');
    Route::put('/groups/{id}', [GroupController::class, 'update'])
        ->middleware('permission:Sistema:Group:update');
    Route::delete('/groups/{id}', [GroupController::class, 'destroy'])
        ->middleware('permission:Sistema:Group:delete');
    Route::post('/users/{user}/groups/{group}', [UserGroupController::class, 'assign'])
        ->middleware('permission:Sistema:User:update');
    Route::delete('/users/{user}/groups/{group}', [UserGroupController::class, 'remove'])
        ->middleware('permission:Sistema:User:update');
    Route::post('/groups/{group}/permissions/{permission}', [GroupPermissionController::class, 'assign'])
        ->middleware('permission:Sistema:Group:update');
    Route::delete('/groups/{group}/permissions/{permission}', [GroupPermissionController::class, 'revoke'])
        ->middleware('permission:Sistema:Group:update');
    Route::get('/groups/{group}/permissions', [GroupPermissionController::class, 'getPermissionsForGroup'])
        ->middleware('permission:Sistema:Group:read');

    Route::apiResource('rols', RolController::class)
        ->only(['index', 'show'])
        ->middleware('permission:Sistema:Rol:read');
    Route::post('/rols', [RolController::class, 'store'])
        ->middleware('permission:Sistema:Rol:create');
    Route::put('/rols/{rol}', [RolController::class, 'update'])
        ->middleware('permission:Sistema:Rol:update');
    Route::delete('/rols/{rol}', [RolController::class, 'destroy'])
        ->middleware('permission:Sistema:Rol:delete');
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:Sistema:Permission:read');
    Route::get('/modules/bombero-accidentado/permissions', [PermissionController::class, 'bomberoAccidentado'])
        ->middleware('permission:Sistema:Permission:read');
    Route::get('/modules/material-mayor/permissions', [PermissionController::class, 'materialMayor'])
        ->middleware('permission:Sistema:Permission:read');
    Route::get('/rols/{rol}/permissions', [RolController::class, 'getPermissions'])
        ->middleware('permission:Sistema:Rol:read');
    Route::post('/rols/{rol}/permissions', [RolController::class, 'syncPermissions'])
        ->middleware('permission:Sistema:Rol:update');

    // === RUTAS DEL MÓDULO MATERIAL MAYOR ===
    Route::apiResource('cars', CarController::class)
        ->only(['index', 'show'])
        ->middleware('permission:Material Mayor:Car:read');
    Route::post('/cars', [CarController::class, 'store'])
        ->middleware('permission:Material Mayor:Car:create');
    Route::put('/cars/{car}', [CarController::class, 'update'])
        ->middleware('permission:Material Mayor:Car:update');
    Route::delete('/cars/{car}', [CarController::class, 'destroy'])
        ->middleware('permission:Material Mayor:Car:delete');
    // --- Reportes (Mantenciones) ---
    Route::post('/cars/{car}/maintenances', [MaintenanceController::class, 'store'])
        ->middleware('permission:Material Mayor:Maintenance:create');
    Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])
        ->middleware('permission:Material Mayor:Maintenance:delete');
    Route::put('/maintenances/{maintenance}', [MaintenanceController::class, 'update'])
        ->middleware('permission:Material Mayor:Maintenance:update');
    // --- Checklists ---
    Route::post('/cars/{car}/checklists', [CarChecklistController::class, 'store'])
        ->middleware('permission:Material Mayor:Checklist:create');
    Route::put('/checklists/{checklist}', [CarChecklistController::class, 'update'])
        ->middleware('permission:Material Mayor:Checklist:update');
    Route::delete('/checklists/{checklist}', [CarChecklistController::class, 'destroy'])
        ->middleware('permission:Material Mayor:Checklist:delete');
    Route::patch('/checklist-items/{item}/toggle', [CarChecklistController::class, 'toggleItem'])
        ->middleware('permission:Material Mayor:Checklist:update');
    // --- Documentos (Gastos) ---
    Route::post('/cars/{car}/documents', [CarDocumentController::class, 'store'])
        ->middleware('permission:Material Mayor:Document:create');
    Route::patch('/documents/{document}/toggle-payment', [CarDocumentController::class, 'togglePayment'])
        ->middleware('permission:Material Mayor:Document:update');
});
