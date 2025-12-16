import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FileUploadService {
  private http = inject(HttpClient);

  private backendUrl = 'http://127.0.0.1:8000/api';

  createProcess(data: { bomberoNombre: string, compania: string }): Observable<any> {
    const payload = {
      bombero_name: data.bomberoNombre,
      company: data.compania
    };
    return this.http.post(`${this.backendUrl}/process`, payload);
  }

  uploadFile(processId: number, stepTitle: string, file: File): Observable<any> {
    const endpoint = this.getEndpointForStep(stepTitle);
    
    if (!endpoint) {
      console.error(`Endpoint no encontrado para el paso: "${stepTitle}"`);
      return of(null); 
    }

    const formData = new FormData();
    // CORREGIDO: Se cambia 'file' por 'document' para que coincida con el backend de Laravel.
    formData.append('document', file, file.name);
    const finalUrl = `${this.backendUrl}/process/${processId}/${endpoint}`;
    return this.http.post(finalUrl, formData);
  }

  getProcessById(id: string): Observable<any> {
    return this.http.get(`${this.backendUrl}/process/${id}`);
  }

  deleteFile(fileId: number): Observable<any> {
    return this.http.delete(`${this.backendUrl}/documents/${fileId}`);
  }

  completeStep(processId: number, stepTitle: string): Observable<any> {
    const url = `${this.backendUrl}/processes/${processId}/complete-step`;
    // El endpoint no requiere payload; se envía objeto vacío.
    return this.http.patch(url, {});
  }

  viewFile(fileId: number): Observable<Blob> {
    return this.http.get(`${this.backendUrl}/documents/${fileId}/view`, {
      responseType: 'blob'
    });
  }
  
  downloadFile(fileId: number): Observable<Blob> {
    return this.http.get(`${this.backendUrl}/documents/${fileId}/download`, {
      responseType: 'blob'
    });
  }

  private getEndpointForStep(stepTitle: string): string | null {
    const endpointMap: { [key: string]: string } = {
      //REQUERIMIENTO OPERATIVO
      'Reporte Flash': 'upload_reporte_flash',
      'DIAB (declaracion Individual de accidente bomberil)': 'upload_diab',
      'Informe del OBAC': 'upload_obac',
      'Declaracion de testigos (si es que aplica)': 'upload_declaracion_testigo',
      'Incidente sin lesiones Copia del Libro de Guardia (no legalizado y solo el registro del accidente)': 'upload_copia_libro_guardia',

      //ANTECEDENTES GENERALES 
      'Certificado de Carabineros.': 'upload_certificado_carabineros',
      'DAU o variantes.': 'upload_dau',
      'Orden de atención médica': 'upload_certificado_medico_atencion_especial',
      'Informe Médico emitido por médico tratante, con diagnóstico y prestaciones': 'upload_informe_medico',
      'Otros Documentos de caracter Médico adicional': 'upload_otros_documento_medico_adicional',

      //DOCUMENTOS DEL CUERPO DE BOMBEROS
      'Certificado Superintendente que acredite calidad voluntario': 'upload_certificado_acreditacion_voluntario',
      'Copia libro de llamadas (central de Alarmas) referido a 3 días previos y posteriores al accidente autorizado ante notario.': 'upload_copia_libro_llamada',
      'Copia Aviso de citación al acto de servicio (ACADEMIA).': 'upload_aviso_citacion',
      'Copia Lista de Asistencia al acto de servicio específico (ACADEMIA), autorizado ante notario.': 'upload_copia_lista_asistencia',
      'Informe Ejecutivo sobre el acto de servicio en que se producen lesiones, suscrito por Superintendente y Comandante.': 'upload_informe_ejecutivo',

      //PRESTACIONES MEDICA
      'Factura establecimiento hospitalario, con detalle de prestaciones.': 'upload_factura_prestaciones',
      'Boletas de Honorarios Profesionales, no incluidas en factura, visadas por medico jefe del servicio.': 'upload_boleta_honorarios_visada',
      'Boleta de Medicamentos y prescripción correspondiente.': 'upload_boleta_medicamentos',
      'Certificado del director del servicio que autoriza exámenes, recetas, medicamentos, controles, traslados, acciones médicas y procedimientos generales...': 'upload_certificado_medico_autorizacion_examen',

      //GASTOS DE TRASLADOS Y ALIMENTACIÓN
      'Boleta o factura de gastos de traslado del voluntario': 'upload_boleta_factura_traslado',
      'Certificado del médico tratante que determine la incapacidad de asistir al voluntario...': 'upload_certificado_medico_incapacidad',
      'Certificado médico tratante que justifique la necesidad de traslado del voluntario y del medio empleado.': 'upload_certificado_medico_traslado',
      'Boleta de gastos de hospedaje del acompañante del voluntario.': 'upload_boleta_gastos_acompanante',
      'Boleta de gastos de alimentación del acompañante del voluntario.': 'upload_boleta_alimentacion_acompanante',
      'Otros (Especificar)': 'upload_otros_gastos',
    };
    
    return endpointMap[stepTitle] || null;
  }
}
