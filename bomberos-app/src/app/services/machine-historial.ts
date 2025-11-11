import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';

const API_URL = 'http://127.0.0.1:8000/api';
export interface MaintenanceLog {
  id: number;
  reported_problem: string;
  inspector_name: string;
  service_date: string;
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

export interface ApiDocument {
  id: number;
  car_id: number;
  cost: number;
  file_name: string; 
  path: string; 
  file_type: 'pdf' | 'img' | 'doc' | 'other';
  url: string; 
  created_at: string;
}

export interface CarApiResponse {
  id: number;
  name: string;
  plate: string;
  model: string | null;
  company: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
  maintenances: MaintenanceLog[];
  checklists: ApiChecklist[];
  documents: ApiDocument[]; 
}

export interface CreateCarDto {
  name: string;
  plate: string;
  model: string | null;
  company: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
}

export interface CreateMaintenanceDto {
  mileage: number;
  service_type: string;
  inspector_name: string;
  service_date: string;
  reported_problem: string;
  activities_detail: string;
  inspector_signature: string;
  officer_signature: string;
  cabin?: string;
  filter_code?: string;
  hourmeter?: string;
  warnings?: string;
  pending_work?: string;
  pending_type?: string;
  observations?: string;
  car_info_annex?: string;}

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

  createUnit(unitData: CreateCarDto): Observable<CarApiResponse> {
    return this.http.post<CarApiResponse>(`${this.apiUrl}/cars`, unitData);
  }


  createMaintenance(carId: number, maintenanceData: CreateMaintenanceDto): Observable<MaintenanceLog> {
    return this.http.post<MaintenanceLog>(
      `${this.apiUrl}/cars/${carId}/maintenances`,
      maintenanceData
    );
  }

  createChecklist(carId: number, checklistData: CreateChecklistDto): Observable<ApiChecklist> {
    return this.http.post<ApiChecklist>(
      `${this.apiUrl}/cars/${carId}/checklists`,
      checklistData
    );
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
}