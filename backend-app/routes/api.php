<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ReporteFlashController;
use App\Http\Controllers\DiabController;
use App\Http\Controllers\ObacController;
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
use App\Http\Controllers\CertificadoMedicoIncapacidadController;
use App\Http\Controllers\DeclaracionTestigoController;
use App\Http\Controllers\DauController;
use App\Http\Controllers\DocumentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
Route::patch('/processes/{process}/complete-step', [ProcessController::class, 'completeStep']);
// Rutas para el CRUD
Route::post('/process', [ProcessController::class, 'store']);
Route::get('/process', [ProcessController::class, 'index']);  
Route::get('/process/{process}', [ProcessController::class, 'show']); 
Route::put('/process/{process}', [ProcessController::class, 'update']);     
Route::delete('/process/{process}', [ProcessController::class, 'destroy']);  
Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
// Requerimiento Operativo
Route::post('process/{id}/upload_reporte_flash', [ReporteFlashController::class, 'store']);
Route::post('process/{id}/upload_diab', [DiabController::class, 'store']);
Route::post('process/{id}/upload_obac', [ObacController::class, 'store']);
Route::post('process/{id}/upload_copia_libro_guardia', [CopiaLibroGuardiaController::class, 'store']);
Route::post('process/{id}/upload_declaracion_testigo', [DeclaracionTestigoController::class, 'store']);
//Antecedentes Generales
Route::post('process/{id}/upload_certificado_carabineros', [CertificadoCarabineroController::class, 'store']);
Route::post('process/{id}/upload_dau', [DauController::class, 'store']);
Route::post('process/{id}/upload_informe_medico', [InformeMedicoController::class, 'store']);
Route::post('process/{id}/upload_otros_documento_medico_adicional', [OtroDocumentoMedicoController::class, 'store']);
Route::post('process/{id}/upload_certificado_medico_atencion_especial', [CertificadoMedicoAtencionEspecialController::class, 'store']);
//Documento del Cuerpo de Bomberos
Route::post('process/{id}/upload_certificado_acreditacion_voluntario', [CertificadoAcreditacionVoluntarioController::class, 'store']);
Route::post('process/{id}/upload_copia_libro_llamada', [CopiaLibroLlamadaController::class, 'store']);
Route::post('process/{id}/upload_aviso_citacion', [AvisoCitacionController::class, 'store']);
Route::post('process/{id}/upload_copia_lista_asistencia', [CopiaListaAsistenciaController::class, 'store']);
Route::post('process/{id}/upload_informe_ejecutivo', [InformeEjecutivoController::class, 'store']);
//Prestaciones Medica
Route::post('process/{id}/upload_factura_prestaciones', [FacturaPrestacionController::class, 'store']);
Route::post('process/{id}/upload_boleta_honorarios_visada', [BoletaHonorarioVisadaController::class, 'store']);
Route::post('process/{id}/upload_boleta_medicamentos', [BoletaMedicamentoController::class, 'store']);
Route::post('process/{id}/upload_certificado_medico_autorizacion_examen', [CertificadoMedicoAutorizacionExamenController::class, 'store']);
//Gastos de Traslados y alimentacion
Route::post('process/{id}/upload_boleta_factura_traslado', [BoletaFacturaController::class, 'store']);
Route::post('process/{id}/upload_certificado_medico_traslado', [CertificadoMedicoTrasladoController::class, 'store']);
Route::post('process/{id}/upload_boleta_gastos_acompanante', [BoletaGastoAcompananteController::class, 'store']);
Route::post('process/{id}/upload_certificado_medico_incapacidad', [CertificadoMedicoIncapacidadController::class, 'store']);
Route::post('process/{id}/upload_boleta_alimentacion_acompanante', [BoletaGastoAcompananteController::class, 'store']);
Route::post('process/{id}/upload_otros_gastos', [OtroGastoController::class, 'store']);