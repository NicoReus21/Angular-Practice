import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

// Helper para asegurar que la URL base sea correcta
const getBaseUrl = () => {
  let url = environment.backendUrl || 'http://127.0.0.1:8000/api';
  // Quitar slash final si existe
  if (url.endsWith('/')) {
    url = url.slice(0, -1);
  }
  // Agregar /api si falta (Solución al error CORS por 404)
  if (!url.endsWith('/api')) {
    url += '/api';
  }
  return url;
};

const API_URL = getBaseUrl();

export interface ApiDocument {
  id: number;
  car_id?: number;
  maintenance_id?: number;
  cost: number;
  file_name: string;
  path: string;
  file_type: 'pdf' | 'img' | 'doc' | 'other';
  url: string;
  created_at: string;
  is_paid: boolean;
}

export interface ApiMaintenance {
  id: number;
  car_id: number;
  service_date: string;
  chassis_number?: string;
  status: 'draft' | 'completed';
  pdf_url?: string | null;
  service_type?: string;
  inspector_name?: string;
  reported_problem?: string;
  mileage?: number;
  cabin?: string;
  filter_code?: string;
  hourmeter?: string;
  warnings?: string;
  location?: string;
  activities_detail?: string;
  pending_work?: string;
  pending_type?: string;
  observations?: string;
  inspector_signature?: string;
  officer_signature?: string;
  car_info_annex?: string;
  documents?: ApiDocument[];
}

export interface ApiChecklistItem {
  id: number;
  checklist_id: number;
  task_description: string;
  completed: boolean;
}

export interface ApiChecklist {
  id: number;
  car_id: number;
  persona_cargo: string;
  fecha_realizacion: string;
  items: ApiChecklistItem[];
}

export interface ChecklistTaskItem {
  id: number;
  task_description: string;
  completed: boolean;
}

export interface ChecklistGroup {
  id: number;
  persona_cargo: string;
  fecha_realizacion: string;
  items: ChecklistTaskItem[];
}

export interface InspectionChecklistItem {
  id: number;
  key: string;
  label: string;
  value: 'yes' | 'no' | 'na';
  comment?: string | null;
}

export interface InspectionCategory {
  id: number;
  key: string;
  label: string;
  sort_order: number;
}

export interface InspectionChecklist {
  id: number;
  car_id: number;
  inspected_at: string | null;
  items: InspectionChecklistItem[];
}

export interface CarApiResponse {
  id: number;
  name: string;
  plate: string;
  model: string | null;
  company: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
  maintenances: ApiMaintenance[];
  checklists: ApiChecklist[];
  documents: ApiDocument[];
  inspection_checklists?: InspectionChecklist[];
  imageUrl: string | null;
  // Campos que vienen del backend
  image?: string | null;
  image_url?: string | null;
  manufacturing_year?: number; // Agregado para tipado fuerte
  chassis_number?: string;     // Agregado para tipado fuerte
}

export interface CreateCarDto {
  name: string;
  plate: string;
  model: string | null;
  company: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
  // CORRECCIÓN: Soportar ambas nomenclaturas para evitar datos perdidos
  manufacturing_year?: number;
  chassis_number?: string;
}

export interface CreateMaintenanceDto {
  service_date: string | Date;
  status: 'draft' | 'completed';
  mileage?: number;
  service_type?: string;
  inspector_name?: string;
  reported_problem?: string;
  activities_detail?: string;
  inspector_signature?: string;
  officer_signature?: string;
  cabin?: string;
  filter_code?: string;
  hourmeter?: string;
  warnings?: string;
  pending_work?: string;
  pending_type?: string;
  observations?: string;
  car_info_annex?: string;
}

export interface VendorReportLinkResponse {
  token: string;
  expires_at: string;
  url: string;
}

export interface CreateChecklistDto {
  persona_cargo: string;
  fecha_realizacion: string;
  tasks: string[];
}

@Injectable({
  providedIn: 'root'
})
export class MachineHistorialService {

  private http = inject(HttpClient);
  // Usa la URL normalizada con /api al final
  private apiUrl = API_URL;

  getUnits(): Observable<CarApiResponse[]> {
    return this.http.get<CarApiResponse[]>(`${this.apiUrl}/cars`);
  }
  
  createUnit(data: CreateCarDto, imageFile: File | null): Observable<CarApiResponse> {
    const formData = new FormData();
    this.appendCarData(formData, data, imageFile);
    return this.http.post<CarApiResponse>(`${this.apiUrl}/cars`, formData);
  }

  updateUnit(id: number, data: CreateCarDto, imageFile: File | null): Observable<CarApiResponse> {
    const formData = new FormData();
    this.appendCarData(formData, data, imageFile);
    formData.append('_method', 'PUT'); 
    return this.http.post<CarApiResponse>(`${this.apiUrl}/cars/${id}`, formData);
  }

  deleteUnit(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/cars/${id}`);
  }

  private appendCarData(formData: FormData, data: CreateCarDto, imageFile: File | null) {
    formData.append('name', data.name);
    formData.append('plate', data.plate);
    if (data.model) formData.append('model', data.model);
    formData.append('company', data.company);
    formData.append('status', data.status);
    
    // CORRECCIÓN ROBUSTA: Buscar valor en snake_case O camelCase
    // Esto asegura que el dato se envíe sin importar cómo venga del formulario
    const year = data.manufacturing_year;
    if (year) {
      formData.append('manufacturing_year', year.toString());
    }

    const chassis = data.chassis_number;
    if (chassis) {
      formData.append('chassis_number', chassis);
    }
    
    if (imageFile) {
      formData.append('image', imageFile, imageFile.name);
    }
  }

  updateUnitStatus(carId: number, status: string): Observable<CarApiResponse> {
    return this.http.patch<CarApiResponse>(`${this.apiUrl}/cars/${carId}`, { status });
  }

  createMaintenance(carId: number, maintenanceData: any): Observable<ApiMaintenance> {
    return this.http.post<ApiMaintenance>(
      `${this.apiUrl}/cars/${carId}/maintenances`,
      maintenanceData
    );
  }

  updateMaintenance(maintenanceId: number, maintenanceData: any): Observable<ApiMaintenance> {
    return this.http.put<ApiMaintenance>(
      `${this.apiUrl}/maintenances/${maintenanceId}`,
      maintenanceData
    );
  }

  updateMaintenanceWithFiles(maintenanceId: number, maintenanceData: FormData): Observable<ApiMaintenance> {
    return this.http.post<ApiMaintenance>(
      `${this.apiUrl}/maintenances/${maintenanceId}`, 
      maintenanceData
    );
  }

  deleteMaintenance(maintenanceId: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/maintenances/${maintenanceId}`);
  }

  createChecklist(carId: number, checklistData: CreateChecklistDto): Observable<ApiChecklist> {
    return this.http.post<ApiChecklist>(
      `${this.apiUrl}/cars/${carId}/checklists`,
      checklistData
    );
  }

  updateChecklist(checklistId: number, checklistData: CreateChecklistDto): Observable<ApiChecklist> {
    return this.http.put<ApiChecklist>(
      `${this.apiUrl}/checklists/${checklistId}`,
      checklistData
    );
  }

  deleteChecklist(checklistId: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/checklists/${checklistId}`);
  }

  toggleChecklistItem(itemId: number): Observable<any> {
    return this.http.patch(`${this.apiUrl}/checklist-items/${itemId}/toggle`, {});
  }

  getInspectionChecklists(carId: number): Observable<InspectionChecklist[]> {
    return this.http.get<InspectionChecklist[]>(`${this.apiUrl}/cars/${carId}/inspection-checklists`);
  }

  createInspectionChecklist(carId: number, payload: any): Observable<InspectionChecklist> {
    return this.http.post<InspectionChecklist>(`${this.apiUrl}/cars/${carId}/inspection-checklists`, payload);
  }

  updateInspectionChecklist(checklistId: number, payload: any): Observable<InspectionChecklist> {
    return this.http.put<InspectionChecklist>(`${this.apiUrl}/inspection-checklists/${checklistId}`, payload);
  }

  deleteInspectionChecklist(checklistId: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/inspection-checklists/${checklistId}`);
  }

  getInspectionCategories(): Observable<InspectionCategory[]> {
    return this.http.get<InspectionCategory[]>(`${this.apiUrl}/inspection-categories`);
  }

  createInspectionCategory(payload: { label: string; key?: string; sort_order?: number }): Observable<InspectionCategory> {
    return this.http.post<InspectionCategory>(`${this.apiUrl}/inspection-categories`, payload);
  }

  updateInspectionCategory(categoryId: number, payload: { label?: string; key?: string; sort_order?: number }): Observable<InspectionCategory> {
    return this.http.put<InspectionCategory>(`${this.apiUrl}/inspection-categories/${categoryId}`, payload);
  }

  deleteInspectionCategory(categoryId: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/inspection-categories/${categoryId}`);
  }

  uploadDocument(carId: number, cost: number, file: File): Observable<ApiDocument> {
    const formData = new FormData();
    formData.append('cost', cost.toString());
    formData.append('file', file, file.name);

    return this.http.post<ApiDocument>(
      `${this.apiUrl}/cars/${carId}/documents`,
      formData
    );
  }

  deleteDocument(documentId: number): Observable<void> {
  
    return this.http.delete<void>(`${this.apiUrl}/documents/${documentId}`);
  }

  toggleDocumentPayment(documentId: number): Observable<ApiDocument> {
    return this.http.patch<ApiDocument>(`${this.apiUrl}/documents/${documentId}/toggle-payment`, {});
  }

  createVendorReportLink(payload: { email: string; car_id: number; expires_in_days: number; name?: string | null }): Observable<VendorReportLinkResponse> {
    return this.http.post<VendorReportLinkResponse>(`${this.apiUrl}/vendor-report-links`, payload);
  }

  getVendorReportLink(token: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/vendor-report-links/${token}`);
  }

  submitVendorReport(token: string, payload: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/vendor-report-links/${token}`, payload);
  }

  downloadMaintenanceDocument(documentId: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/maintenance-documents/${documentId}/download`, {
      responseType: 'blob',
    });
  }

  downloadMaintenancePdf(maintenanceId: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/maintenances/${maintenanceId}/pdf`, {
      responseType: 'blob',
    });
  }
}