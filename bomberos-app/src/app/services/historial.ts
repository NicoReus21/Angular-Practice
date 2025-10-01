import { Injectable, signal, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { UploadSection } from '../components/document-upload/document-upload';

export interface HistorialElement {
  id: number;
  bombero_nombre: string; 
  created_at: string;
  estado: string;
  compania: string;
  sections_data: string | UploadSection[]; 
}

@Injectable({
  providedIn: 'root'
})
export class HistorialService {
  private http = inject(HttpClient);
  private backendUrl = 'http://localhost:8000/api/process';

  private records = signal<HistorialElement[]>([]);
  public readonly historyRecords = this.records.asReadonly();

  constructor() {
    this.fetchHistory().subscribe({
      error: (err) => console.error('Error al cargar el historial inicial:', err)
    });
  }

  fetchHistory(): Observable<HistorialElement[]> {
    console.log('Fetching history from backend...');
    return this.http.get<HistorialElement[]>(this.backendUrl).pipe(
      tap(data => {
        console.log('History data received:', data);
        this.records.set(data);
      })
    );
  }

  getRecordById(id: string): HistorialElement | undefined {
    return this.records().find(record => record.id === Number(id));
  }

  deleteRecord(id: number): Observable<any> {
    return this.http.delete(`${this.backendUrl}/${id}`);
  }
}