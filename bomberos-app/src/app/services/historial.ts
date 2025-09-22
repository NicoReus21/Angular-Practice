import { Injectable, signal } from '@angular/core';
import { UploadSection } from '../components/document-upload/document-upload';

// La interfaz ahora incluye las secciones con sus pasos y archivos
export interface HistorialElement {
  id: string;
  nombre: string;
  fecha: string;
  estado: string;
  compania: string;
  sections: UploadSection[]; // Guardamos el estado completo aquí
}

// Datos iniciales de ejemplo
const INITIAL_DATA: HistorialElement[] = [
  {id: 'SBA-001', nombre: 'Juan Pérez', fecha: '20/08/2025', estado: 'Completado', compania: 'Primera', sections: []},
  {id: 'SBA-002', nombre: 'María González', fecha: '25/08/2025', estado: 'En Revisión', compania: 'Tercera', sections: []},
];

@Injectable({
  providedIn: 'root'
})
export class HistorialService {
  private records = signal<HistorialElement[]>(INITIAL_DATA);
  public readonly historyRecords = this.records.asReadonly();

  constructor() { }
  
  /**
   * Busca y devuelve un registro por su ID.
   */
  getRecordById(id: string): HistorialElement | undefined {
    return this.records().find(record => record.id === id);
  }

  /**
   * Añade un nuevo registro al historial.
   */
  addRecord(record: Omit<HistorialElement, 'id' | 'fecha' | 'estado'>): void {
    const newId = `SBA-${(this.records().length + 1).toString().padStart(3, '0')}`;
    const newRecord: HistorialElement = {
      ...record,
      id: newId,
      fecha: new Date().toLocaleDateString('es-CL'),
      estado: 'En Revisión'
    };
    this.records.update(currentRecords => [...currentRecords, newRecord]);
  }

  /**
   * Actualiza un registro existente en el historial.
   */
  updateRecord(updatedRecord: HistorialElement): void {
    this.records.update(currentRecords => 
      currentRecords.map(rec => rec.id === updatedRecord.id ? updatedRecord : rec)
    );
  }
}

