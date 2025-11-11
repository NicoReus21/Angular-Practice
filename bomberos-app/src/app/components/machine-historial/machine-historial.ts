import {
  Component,
  ChangeDetectionStrategy,
  signal,
  inject,
  computed,
  ViewChild,
  ElementRef,
  OnInit,
} from '@angular/core';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { FormsModule } from '@angular/forms';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatListModule } from '@angular/material/list';
import { MatCardModule } from '@angular/material/card';
import { MatTabsModule } from '@angular/material/tabs';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatButtonToggleModule } from '@angular/material/button-toggle';
import { MatDividerModule } from '@angular/material/divider';
import { MatRippleModule } from '@angular/material/core';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar'; 
import {
  MachineHistorialService,
  CarApiResponse,
  ApiChecklist,
  CreateChecklistDto,
  CreateCarDto,
  CreateMaintenanceDto,
  ApiDocument,
} from '../../services/machine-historial';
import { CreateFiretruckComponent } from '../create-firetruck/create-firetruck';
import { CreateReportComponent } from '../create-report/create-report';
import { CreateChecklistComponent, ChecklistTask } from '../create-checklist/create-checklist';


export interface MaintenanceLog {
  id: number;
  date: string; 
  technician: string;
  description: string;
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

export interface AttachedDocument {
  id: number;
  name: string; 
  type: 'pdf' | 'doc' | 'img' | 'other';
  url: string;
  uploaded_at_formatted: string; 
  cost: number;
}

export interface VehicleUnit {
  id: number;
  name: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
  model: string | null;
  plate: string;
  company: string;
  checklists: ChecklistGroup[];
  documents: AttachedDocument[];
  maintenanceHistory: MaintenanceLog[];
}

@Component({
  selector: 'app-machine-historial',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    HttpClientModule, 
    MatSnackBarModule, 
    MatSidenavModule,
    MatListModule,
    MatCardModule,
    MatTabsModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatButtonToggleModule,
    MatDividerModule,
    MatRippleModule,
    MatDialogModule,
    MatExpansionModule,
    MatFormFieldModule,
    MatInputModule,
    MatTooltipModule,
    CurrencyPipe,
  ],
  templateUrl: './machine-historial.html',
  styleUrls: ['./machine-historial.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class MachineHistorialComponent implements OnInit {
  private dialog = inject(MatDialog);
  private vehicleService = inject(MachineHistorialService); 
  private snackBar = inject(MatSnackBar); 

  @ViewChild('fileInput') fileInputRef!: ElementRef<HTMLInputElement>;

  private allUnits = signal<VehicleUnit[]>([]);
  selectedUnitId = signal<number | null>(null);
  currentStatusFilter = signal<'Todos' | 'En Servicio' | 'En Taller' | 'Fuera de Servicio'>(
    'Todos'
  );

  newDocumentName = signal<string>('');
  newDocumentCost = signal<number | null>(null);
  newDocumentFile = signal<File | null>(null); 
  isUploading = signal(false); 


  filteredUnits = computed(() => {
    const status = this.currentStatusFilter();
    if (status === 'Todos') {
      return this.allUnits();
    }
    return this.allUnits().filter((unit) => unit.status === status);
  });

  selectedUnit = computed(() => {
    const id = this.selectedUnitId();
    return this.allUnits().find((u) => u.id === id) || null;
  });

  totalDocumentsCost = computed(() => {
    const unit = this.selectedUnit();
    if (!unit || !unit.documents) {
      return 0;
    }
    return unit.documents.reduce((sum, doc) => sum + (doc.cost || 0), 0);
  });

  // --- INICIALIZACIÓN ---
  ngOnInit(): void {
    this.loadUnits();
  }

  loadUnits(): void {
    this.vehicleService.getUnits().subscribe({
      next: (unitsFromApi) => {
        const mappedUnits: VehicleUnit[] = unitsFromApi.map(car => this.mapApiCarToVehicleUnit(car));
        
        this.allUnits.set(mappedUnits);
        
        // Auto-seleccionar la primera unidad si no hay ninguna seleccionada
        if (!this.selectedUnitId() && mappedUnits.length > 0) {
          this.selectedUnitId.set(mappedUnits[0].id);
        }
      },
      error: (err) => {
        console.error('Error al cargar unidades:', err);
        this.snackBar.open('Error al cargar las unidades desde el servidor.', 'Cerrar', {
          duration: 5000,
          panelClass: 'error-snackbar'
        });
      }
    });
  }

  private mapApiCarToVehicleUnit(car: CarApiResponse): VehicleUnit {
    return {
      id: car.id,
      name: car.name,
      status: car.status, 
      model: car.model,
      plate: car.plate,
      company: car.company,
      
      checklists: (car.checklists || []).map(cl => ({
        id: cl.id,
        persona_cargo: cl.persona_cargo,
        fecha_realizacion: new Date(cl.fecha_realizacion).toLocaleDateString('es-CL'),
        items: (cl.items || []).map(item => ({
          id: item.id,
          task_description: item.task_description,
          completed: item.completed
        }))
      })),
      
      documents: (car.documents || []).map(doc => this.mapApiDocumentToLocal(doc)),
      maintenanceHistory: (car.maintenances || []).map(m => ({
        id: m.id,
        date: new Date(m.service_date).toLocaleDateString('es-CL'),
        technician: m.inspector_name,
        description: m.reported_problem,
      }))
    };
  }


  private mapApiDocumentToLocal(doc: ApiDocument): AttachedDocument {
    return {
      id: doc.id,
      name: doc.file_name,
      type: doc.file_type,
      url: doc.url,
      uploaded_at_formatted: new Date(doc.created_at).toLocaleDateString('es-CL'),
      cost: +doc.cost, 
    };
  }

  private getFirstErrorMessage(err: any, defaultMsg: string = 'Error desconocido'): string {
    if (!err) {
      return defaultMsg;
    }

    if (err.error?.errors) {
      try {
        const allErrorArrays = Object.values(err.error.errors) as string[][];
        if (allErrorArrays.length > 0 && allErrorArrays[0].length > 0) {
          return allErrorArrays[0][0];
        }
      } catch (e) {
        console.error('Error al parsear el objeto de validación:', e);
      }
    }

    if (typeof err.error?.message === 'string') {
      return err.error.message;
    }

    if (typeof err.message === 'string') {
      return err.message;
    }
    
    return defaultMsg;
  }


  // --- MANEJO DE EVENTOS (sin cambios) ---
  onFilterChange(status: 'Todos' | 'En Servicio' | 'En Taller' | 'Fuera de Servicio'): void {
    this.currentStatusFilter.set(status);
  }

  onSelectUnit(id: number): void {
    this.selectedUnitId.set(id);
  }

  getStatusChipClass(status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio'): string {
    switch (status) {
      case 'En Servicio': return 'status-chip-servicio';
      case 'En Taller': return 'status-chip-taller';
      case 'Fuera de Servicio': return 'status-chip-fuera';
    }
  }

  getDocumentIcon(type: 'pdf' | 'doc' | 'img' | 'other'): string {
    switch (type) {
      case 'pdf': return 'picture_as_pdf';
      case 'doc': return 'description';
      case 'img': return 'image';
      case 'other': return 'attach_file';
    }
  }

  // --- LÓGICA DE CHECKLIST (sin cambios en la conexión) ---
  onChecklistItemToggle(checklistGroupId: number, taskId: number): void {
    // TODO: Conectar a la API
    console.warn('TODO: Conectar onChecklistItemToggle a la API');
    this.allUnits.update((units) => {
      return [...units];
    });
  }

  onEditChecklist(checklistId: number, event: MouseEvent): void {
    console.warn('TODO: Implementar edición de checklist');
  }

  onDeleteChecklist(checklistId: number, event: MouseEvent): void {
    // TODO: Conectar a la API
    console.warn('TODO: Conectar onDeleteChecklist a la API');

  }


  openCreateUnitDialog(): void {
    const dialogRef = this.dialog.open(CreateFiretruckComponent, {
      width: '500px',
      autoFocus: false,
    });

    dialogRef.afterClosed().subscribe((formData: CreateCarDto) => {
      if (formData) {
        this.vehicleService.createUnit(formData).subscribe({
          next: (newCarFromApi) => {
            const newUnit = this.mapApiCarToVehicleUnit(newCarFromApi);
            this.allUnits.update((units) => [newUnit, ...units]);
            this.selectedUnitId.set(newUnit.id);
            this.snackBar.open('Unidad creada con éxito', 'Cerrar', {
              duration: 3000, panelClass: 'success-snackbar'
            });
          },
          error: (err) => {
            console.error('Error al crear la unidad:', err);
            const errorMsg = this.getFirstErrorMessage(err);
            this.snackBar.open(`Error: ${errorMsg}`, 'Cerrar', {
              duration: 5000, panelClass: 'error-snackbar'
            });
          }
        });
      }
    });
  }

  openCreateReportDialog(): void {
    const unit = this.selectedUnit();
    if (!unit) return;

    const dialogRef = this.dialog.open(CreateReportComponent, {
      width: '800px',
      autoFocus: false,
      data: { unit: { ...unit } },
    });

    dialogRef.afterClosed().subscribe((result: { formData: CreateMaintenanceDto, files: File[] }) => {
      if (result && result.formData) {
        this.vehicleService.createMaintenance(unit.id, result.formData).subscribe({
          next: (newMaintenance) => {
            const newLog: MaintenanceLog = {
              id: newMaintenance.id,
              date: new Date(newMaintenance.service_date).toLocaleDateString('es-CL'),
              technician: newMaintenance.inspector_name,
              description: newMaintenance.reported_problem,
            };
            this.allUnits.update((units) => {
              const unitToUpdate = units.find((u) => u.id === this.selectedUnitId());
              if (unitToUpdate) {
                unitToUpdate.maintenanceHistory.unshift(newLog);
              }
              return [...units];
            });
            this.snackBar.open('Reporte guardado con éxito', 'Cerrar', {
              duration: 3000, panelClass: 'success-snackbar'
            });
            // TODO: Manejar la subida de 'result.files'
            if (result.files.length > 0) {
              console.warn('TODO: Implementar subida de archivos para reportes');
            }
          },
          error: (err) => {
            console.error('Error al guardar el reporte:', err);
            const errorMsg = this.getFirstErrorMessage(err);
            this.snackBar.open(`Error: ${errorMsg}`, 'Cerrar', {
              duration: 5000, panelClass: 'error-snackbar'
            });
          }
        });
      }
    });
  }

  openCreateChecklistDialog(): void {
    const unit = this.selectedUnit();
    if (!unit) return;

    const dialogRef = this.dialog.open(CreateChecklistComponent, {
      width: '600px',
      autoFocus: false,
    });

    dialogRef.afterClosed().subscribe((result: { formData: any, tasks: ChecklistTask[] }) => {
      if (result && result.formData && result.tasks.length > 0) {
        
        const taskStrings = result.tasks.map(t => t.task);
        const dto: CreateChecklistDto = {
          persona_cargo: result.formData.personaCargo,
          fecha_realizacion: result.formData.fechaRealizacion.toISOString().split('T')[0],
          tasks: taskStrings,
        };

        this.vehicleService.createChecklist(unit.id, dto).subscribe({
          next: (newChecklistFromApi) => {
            const newChecklistGroup: ChecklistGroup = {
              id: newChecklistFromApi.id,
              persona_cargo: newChecklistFromApi.persona_cargo,
              fecha_realizacion: new Date(newChecklistFromApi.fecha_realizacion).toLocaleDateString('es-CL'),
              items: newChecklistFromApi.items.map(item => ({
                id: item.id,
                task_description: item.task_description,
                completed: item.completed
              }))
            };
            this.allUnits.update((units) => {
              const unitToUpdate = units.find((u) => u.id === this.selectedUnitId());
              if (unitToUpdate) {
                unitToUpdate.checklists.unshift(newChecklistGroup);
              }
              return [...units];
            });
            this.snackBar.open('Checklist guardado con éxito', 'Cerrar', {
              duration: 3000, panelClass: 'success-snackbar'
            });
          },
          error: (err) => {
            console.error('Error al guardar el checklist:', err);
            const errorMsg = this.getFirstErrorMessage(err);
            this.snackBar.open(`Error: ${errorMsg}`, 'Cerrar', {
              duration: 5000, panelClass: 'error-snackbar'
            });
          }
        });
      }
    });
  }

  clearFileSelection(): void {
    this.newDocumentName.set('');
    this.newDocumentFile.set(null);
    if (this.fileInputRef && this.fileInputRef.nativeElement) {
      this.fileInputRef.nativeElement.value = '';
    }
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      const file = input.files[0];
      this.newDocumentName.set(file.name);
      this.newDocumentFile.set(file);
    } else {
      this.newDocumentName.set('');
      this.newDocumentFile.set(null);
    }
  }

  onAddDocument(): void {
    const cost = this.newDocumentCost();
    const file = this.newDocumentFile();
    const unitId = this.selectedUnitId();

    if (!unitId || cost === null || cost < 0 || !file) {
      console.warn('Faltan datos para añadir el documento (archivo o costo no válido).');
      this.snackBar.open('Debe ingresar un monto y adjuntar un archivo.', 'Cerrar', {
        duration: 3000, panelClass: 'error-snackbar'
      });
      return;
    }

    this.isUploading.set(true);

    this.vehicleService.uploadDocument(unitId, cost, file).subscribe({
      next: (newDocFromApi) => {
        const newDoc = this.mapApiDocumentToLocal(newDocFromApi);
        this.allUnits.update((units) => {
          return units.map((unit) => {
            if (unit.id !== unitId) {
              return unit;
            }
            return {
              ...unit,
              documents: [...unit.documents, newDoc], 
            };
          });
        });
        this.clearFileSelection();
        this.newDocumentCost.set(null);
        this.isUploading.set(false);
        this.snackBar.open('Documento subido con éxito.', 'Cerrar', {
          duration: 3000, panelClass: 'success-snackbar'
        });
      },
      error: (err) => {
        console.error('Error al subir el documento:', err);
        const errorMsg = this.getFirstErrorMessage(err, 'Error al subir el archivo.');
        this.snackBar.open(`Error: ${errorMsg}`, 'Cerrar', {
          duration: 5000, panelClass: 'error-snackbar'
        });
        this.isUploading.set(false); 
      }
    });
  }

  onDeleteDocument(docId: number): void {
    const unitId = this.selectedUnitId();
    if (!unitId) return;
    this.vehicleService.deleteDocument(docId).subscribe({
      next: () => {
        this.allUnits.update((units) => {
          return units.map((unit) => {
            if (unit.id !== unitId) {
              return unit;
            }
            return {
              ...unit,
              documents: unit.documents.filter((doc) => doc.id !== docId),
            };
          });
        });
        this.snackBar.open('Documento eliminado.', 'Cerrar', {
          duration: 3000, panelClass: 'success-snackbar'
        });
      },
      error: (err) => {
        console.error('Error al eliminar el documento:', err);
        this.snackBar.open('Error al eliminar el documento.', 'Cerrar', {
          duration: 5000, panelClass: 'error-snackbar'
        });
      }
    });
  }

  onDownloadDocument(url: string): void {
    if (url) {
      window.open(url, '_blank', 'noopener,noreferrer');
    }
  }
}