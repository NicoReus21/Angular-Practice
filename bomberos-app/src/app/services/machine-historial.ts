import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

const API_URL = environment.apiUrl;

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
  imageUrl: string | null;
  // Variaciones que puede enviar el backend
  image?: string | null;
  image_url?: string | null;
}

export interface CreateCarDto {
  name: string;
  plate: string;
  model: string | null;
  company: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
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
