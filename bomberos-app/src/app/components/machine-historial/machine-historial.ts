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
import { FormsModule } from '@angular/forms';

import { MatDialog, MatDialogModule } from '@angular/material/dialog';
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
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatMenuModule } from '@angular/material/menu';
import { BreakpointObserver } from '@angular/cdk/layout';
// Importaciones para el Datepicker
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, provideNativeDateAdapter } from '@angular/material/core';

import Swal from 'sweetalert2';
import { environment } from '../../../environments/environment';

import {
  MachineHistorialService,
  CarApiResponse,
  CreateChecklistDto,
  CreateCarDto,
  ApiDocument,
  ApiMaintenance,
  ChecklistGroup,
} from '../../services/machine-historial';

import { CreateFiretruckComponent } from '../create-firetruck/create-firetruck';
import { CreateReportComponent } from '../create-report/create-report';
import { CreateChecklistComponent } from '../create-checklist/create-checklist';

export interface MaintenanceLog {
  id: number;
  date: string;
  technician: string;
  description: string;
  service_type: string;
  pdf_url: string | null;
  status: 'draft' | 'completed';
  fullData?: ApiMaintenance;
}

export interface AttachedDocument {
  id: number;
  name: string;
  type: 'pdf' | 'doc' | 'img' | 'other';
  url: string;
  uploaded_at_formatted: string;
  created_at_raw: Date; 
  cost: number;
  is_paid: boolean;
  maintenance_id?: number;
}

export interface VehicleUnit {
  id: number;
  name: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
  model: string | null;
  plate: string;
  company: string;
  imageUrl: string | null;
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
    MatToolbarModule,
    MatMenuModule,
    MatDatepickerModule,
    MatNativeDateModule,
  ],
  templateUrl: './machine-historial.html',
  styleUrls: ['./machine-historial.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()],
})
export class MachineHistorialComponent implements OnInit {
  private dialog = inject(MatDialog);
  private vehicleService = inject(MachineHistorialService);
  private snackBar = inject(MatSnackBar);
  private breakpointObserver = inject(BreakpointObserver);

  @ViewChild('fileInput') fileInputRef!: ElementRef<HTMLInputElement>;

  private allUnits = signal<VehicleUnit[]>([]);
  selectedUnitId = signal<number | null>(null);
  currentStatusFilter = signal<'Todos' | 'En Servicio' | 'En Taller' | 'Fuera de Servicio'>('Todos');

  // SeÃ±ales para el filtro de rango de fechas
  documentsDateStart = signal<Date | null>(null);
  documentsDateEnd = signal<Date | null>(null);

  // SeÃ±ales para nuevo documento
  newDocumentName = signal<string>('');
  newDocumentCost = signal<number | null>(null);
  newDocumentFile = signal<File | null>(null);
  isUploading = signal(false);

  vendorEmail = signal<string>('');
  vendorName = signal<string>('');
  vendorExpiresDays = signal<number>(7);
  vendorLink = signal<string | null>(null);
  vendorLinkExpiresAt = signal<string | null>(null);
  vendorLinkError = signal<string | null>(null);
  isCreatingVendorLink = signal(false);

  isMobile = signal<boolean>(false);
  sidenavOpened = signal<boolean>(true);

  private backendUrl = environment.backendUrl;

  filteredUnits = computed(() => {
    const status = this.currentStatusFilter();
    if (status === 'Todos') return this.allUnits();
    return this.allUnits().filter((unit) => unit.status === status);
  });

  selectedUnit = computed(() => {
    const id = this.selectedUnitId();
    return this.allUnits().find((u) => u.id === id) || null;
  });

  filteredDocuments = computed(() => {
    const unit = this.selectedUnit();
    if (!unit || !unit.documents) return [];

    const start = this.documentsDateStart();
    const end = this.documentsDateEnd();

    if (!start && !end) return unit.documents;

    return unit.documents.filter((doc) => {
      const docDate = new Date(doc.created_at_raw);
      const docTime = docDate.getTime();

      let isValid = true;
      
      if (start) {
        const s = new Date(start);
        s.setHours(0, 0, 0, 0); 
        isValid = isValid && docTime >= s.getTime();
      }
      
      if (end) {
        const e = new Date(end);
        e.setHours(23, 59, 59, 999); 
        isValid = isValid && docTime <= e.getTime();
      }
      return isValid;
    });
  });

  totalDocumentsCost = computed(() => {
    return this.filteredDocuments().reduce((sum, doc) => sum + (doc.cost || 0), 0);
  });

  ngOnInit(): void {
    this.loadUnits();
    this.breakpointObserver.observe(['(max-width: 960px)']).subscribe((result) => {
      const isSmallScreen = result.matches;
      this.isMobile.set(isSmallScreen);
      this.sidenavOpened.set(!isSmallScreen);
    });
  }

  toggleSidenav(): void {
    this.sidenavOpened.update((v) => !v);
  }

  onSelectUnit(id: number) {
    this.selectedUnitId.set(id);
    this.documentsDateStart.set(null);
    this.documentsDateEnd.set(null);
    this.vendorEmail.set('');
    this.vendorName.set('');
    this.vendorExpiresDays.set(7);
    this.vendorLink.set(null);
    this.vendorLinkExpiresAt.set(null);
    this.vendorLinkError.set(null);
    
    if (this.isMobile()) {
      this.sidenavOpened.set(false);
    }
  }

  onFilterChange(status: any) {
    this.currentStatusFilter.set(status);
  }

  clearDateFilter(event: Event) {
    event.stopPropagation();
    this.documentsDateStart.set(null);
    this.documentsDateEnd.set(null);
  }

  getStatusChipClass(status: string): string {
    switch (status) {
      case 'En Servicio': return 'status-chip-servicio';
      case 'En Taller': return 'status-chip-taller';
      case 'Fuera de Servicio': return 'status-chip-fuera';
      default: return '';
    }
  }

  getDocumentIcon(type: string): string {
    switch (type) {
      case 'pdf': return 'picture_as_pdf';
      case 'doc': return 'description';
      case 'img': return 'image';
      default: return 'attach_file';
    }
  }

  onChangeStatus(newStatus: 'En Servicio' | 'En Taller' | 'Fuera de Servicio'): void {
    const unit = this.selectedUnit();
    if (!unit || unit.status === newStatus) return;
    this.vehicleService.updateUnitStatus(unit.id, newStatus).subscribe({
      next: () => {
        this.snackBar.open(`Estado cambiado a: ${newStatus}`, 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
        this.loadUnits();
      },
      error: (err) => {
        this.snackBar.open(`Error al cambiar estado: ${this.getFirstErrorMessage(err)}`, 'Cerrar', { duration: 5000, panelClass: 'error-snackbar' });
      },
    });
  }

  loadUnits(): void {
    this.vehicleService.getUnits().subscribe({
      next: (unitsFromApi) => {
        const mappedUnits: VehicleUnit[] = unitsFromApi.map((car) => this.mapApiCarToVehicleUnit(car));
        this.allUnits.set(mappedUnits);
      },
      error: (err) => {
        console.error('Error al cargar unidades:', err);
        this.snackBar.open('Error al cargar las unidades.', 'Cerrar', { duration: 5000 });
      },
    });
  }

  onEditUnit(): void {
    const unit = this.selectedUnit();
    if (!unit) return;

    const isMobile = this.isMobile();
    const dialogRef = this.dialog.open(CreateFiretruckComponent, {
      width: isMobile ? '95vw' : '600px',
      maxWidth: '100vw',
      maxHeight: '95vh',
      autoFocus: false,
      data: { unit: unit }
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result && result.formData && result.id) {
        this.vehicleService.updateUnit(result.id, result.formData, result.imageFile).subscribe({
          next: (updatedCar) => {
            this.allUnits.update((units) => 
              units.map(u => u.id === updatedCar.id ? this.mapApiCarToVehicleUnit(updatedCar) : u)
            );
            this.snackBar.open('Unidad actualizada correctamente', 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
          },
          error: (err) => {
            this.snackBar.open(`Error al actualizar: ${this.getFirstErrorMessage(err)}`, 'Cerrar', { duration: 5000, panelClass: 'error-snackbar' });
          }
        });
      }
    });
  }

  onDeleteUnit(): void {
    const unit = this.selectedUnit();
    if (!unit) return;

    Swal.fire({
      title: `Â¿Eliminar ${unit.name}?`,
      text: 'Se eliminarÃ¡ la unidad y todo su historial. Esta acciÃ³n no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'SÃ­, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.vehicleService.deleteUnit(unit.id).subscribe({
          next: () => {
            this.allUnits.update(units => units.filter(u => u.id !== unit.id));
            this.selectedUnitId.set(null);
            Swal.fire('Eliminado', 'La unidad ha sido eliminada.', 'success');
          },
          error: (err) => {
            Swal.fire('Error', 'No se pudo eliminar la unidad.', 'error');
            console.error(err);
          }
        });
      }
    });
  }

  private mapApiCarToVehicleUnit(car: CarApiResponse): VehicleUnit {
    const rawUrl = this.mapApiUrl(
      // Soportar distintas formas de devolver la URL desde la API
      (car as any).imageUrl || (car as any).image_url || (car as any).image
    );
    // Truco para romper caché de imagen
    const imageUrl = rawUrl ? `${rawUrl}?t=${new Date().getTime()}` : null;

    return {
      id: car.id,
      name: car.name,
      status: car.status,
      model: car.model,
      plate: car.plate,
      company: car.company,
      imageUrl: imageUrl, 
      checklists: (car.checklists || []).map((cl) => ({
        id: cl.id,
        persona_cargo: cl.persona_cargo,
        fecha_realizacion: new Date(cl.fecha_realizacion).toLocaleDateString('es-CL'),
        items: (cl.items || []).map((item) => ({
          id: item.id,
          task_description: item.task_description,
          completed: item.completed,
        })),
      })),
      documents: (car.documents || []).map((doc) => this.mapApiDocumentToLocal(doc)),
      maintenanceHistory: (car.maintenances || []).map((m: ApiMaintenance) => ({
        id: m.id,
        date: new Date(m.service_date).toLocaleDateString('es-CL'),
        technician: m.inspector_name || 'Borrador',
        description: m.reported_problem || 'Sin descripciÃ³n',
        service_type: m.service_type || 'Borrador',
        pdf_url: this.mapApiUrl(m.pdf_url),
        status: m.status,
        fullData: m, 
      })),
    };
  }

  private mapApiDocumentToLocal(doc: ApiDocument): AttachedDocument {
    const dateSource = (doc as any).date || doc.created_at;
    const dateObj = new Date(dateSource);

    return {
      id: doc.id,
      name: doc.file_name,
      type: doc.file_type,
      url: this.mapApiUrl(doc.url) || doc.url,
      uploaded_at_formatted: dateObj.toLocaleDateString('es-CL'),
      created_at_raw: dateObj, 
      cost: +doc.cost,
      is_paid: !!doc.is_paid,
      maintenance_id: doc.maintenance_id,
    };
  }

  private mapApiUrl(url: string | null | undefined): string | null {
    if (!url) return null;
    if (url.startsWith('http')) return this.normalizeBackendUrl(url);
    if (url.startsWith('/')) return `${this.backendUrl}${url}`;
    // Si viene sin slash inicial (ej: "storage/..."), se asume relativo al backend.
    return `${this.backendUrl}/${url}`;
  }

  private normalizeBackendUrl(url: string): string {
    // Reemplaza host/puerto localhost por el backend configurado
    try {
      const backend = new URL(this.backendUrl);
      const current = new URL(url, this.backendUrl);

      const isLocalHost =
        current.hostname === 'localhost' ||
        current.hostname === '127.0.0.1';

      if (isLocalHost) {
        current.hostname = backend.hostname;
        current.protocol = backend.protocol;
        current.port = backend.port;
      }
      return current.toString();
    } catch {
      return url;
    }
  }

  private getFirstErrorMessage(err: any, defaultMsg: string = 'Error desconocido'): string {
    if (!err) return defaultMsg;
    if (err.error?.errors) {
      try {
        const allErrorArrays = Object.values(err.error.errors) as string[][];
        if (allErrorArrays.length > 0 && allErrorArrays[0].length > 0) {
          return allErrorArrays[0][0];
        }
      } catch (e) {
        console.error('Error al parsear validaciÃ³n:', e);
      }
    }
    return err.message || defaultMsg;
  }

  
  onChecklistItemToggle(checklistGroupId: number, taskId: number): void {
    this.allUnits.update((units) => {
      return units.map((unit) => {
        if (unit.id === this.selectedUnitId()) {
          return {
            ...unit,
            checklists: unit.checklists.map((cl) => {
              if (cl.id === checklistGroupId) {
                return {
                  ...cl,
                  items: cl.items.map((item) => {
                    if (item.id === taskId) {
                      return { ...item, completed: !item.completed };
                    }
                    return item;
                  }),
                };
              }
              return cl;
            }),
          };
        }
        return unit;
      });
    });

    this.vehicleService.toggleChecklistItem(taskId).subscribe({
      error: (err) => {
        console.error('Error al guardar toggle:', err);
        this.snackBar.open('Error al guardar estado. Revertiendo...', 'Cerrar', { duration: 3000 });
        this.loadUnits();
      },
    });
  }

  openCreateChecklistDialog(): void {
    const unit = this.selectedUnit();
    if (!unit) return;
    const isMobile = this.isMobile();

    const dialogRef = this.dialog.open(CreateChecklistComponent, {
      width: isMobile ? '95vw' : '600px',
      maxWidth: '100vw',
      maxHeight: '95vh',
      autoFocus: false,
      panelClass: 'custom-dialog-container'
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result?.formData && result?.tasks?.length > 0) {
        const dto: CreateChecklistDto = {
          persona_cargo: result.formData.personaCargo,
          fecha_realizacion:
            result.formData.fechaRealizacion instanceof Date
              ? result.formData.fechaRealizacion.toISOString().split('T')[0]
              : result.formData.fechaRealizacion,
          tasks: result.tasks.map((t: any) => t.task),
        };

        this.vehicleService.createChecklist(unit.id, dto).subscribe({
          next: () => {
            this.loadUnits();
            this.snackBar.open('Checklist creado', 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
          },
          error: (err) => {
            this.snackBar.open(`Error: ${this.getFirstErrorMessage(err)}`, 'Cerrar', { duration: 5000, panelClass: 'error-snackbar' });
          },
        });
      }
    });
  }

  onEditChecklist(checklistId: number, event: MouseEvent): void {
    event.stopPropagation();
    const unit = this.selectedUnit();
    if (!unit) return;
    const currentChecklist = unit.checklists.find((c) => c.id === checklistId);
    if (!currentChecklist) return;

    const dialogRef = this.dialog.open(CreateChecklistComponent, {
      width: '600px',
      autoFocus: false,
      data: { editMode: true, checklist: currentChecklist },
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result?.formData && result?.tasks?.length > 0) {
        this.vehicleService
          .updateChecklist(checklistId, {} as any)
          .subscribe({ next: () => this.loadUnits() });
      }
    });
  }

  onDeleteChecklist(checklistId: number, event: MouseEvent): void {
    event.stopPropagation();
    Swal.fire({
      title: 'Â¿Eliminar Checklist?',
      text: 'Se eliminarÃ¡ permanentemente.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'SÃ­, eliminar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (result.isConfirmed) {
        this.vehicleService.deleteChecklist(checklistId).subscribe({
          next: () => {
            this.loadUnits();
            Swal.fire('Â¡Eliminado!', 'Checklist eliminado.', 'success');
          },
        });
      }
    });
  }

  openCreateReportDialog(): void {
    const unit = this.selectedUnit();
    if (!unit) return;
    const isMobile = this.isMobile();

    const dialogRef = this.dialog.open(CreateReportComponent, {
      width: isMobile ? '95vw' : '800px',
      maxWidth: '100vw',
      maxHeight: '95vh',
      autoFocus: false,
      data: { unit: { ...unit } },
    });

    this.handleReportDialogClose(dialogRef, unit.id, 'create');
  }

  onEditReportDraft(report: MaintenanceLog): void {
    const unit = this.selectedUnit();
    if (!unit) return;
    const dialogRef = this.dialog.open(CreateReportComponent, {
      width: '800px',
      autoFocus: false,
      data: { unit: { ...unit }, editMode: true, reportData: report.fullData },
    });
    this.handleReportDialogClose(dialogRef, unit.id, 'update', report.id);
  }

  private handleReportDialogClose(
    dialogRef: any,
    unitId: number,
    mode: 'create' | 'update',
    reportId?: number
  ) {
    dialogRef.afterClosed().subscribe((result: { formData: any; files: File[] }) => {
      if (result && result.formData) {
        const formData = new FormData();
        const dto = { ...result.formData };
        if (dto.service_date instanceof Date) {
          dto.service_date = dto.service_date.toISOString().split('T')[0];
        }
        Object.keys(dto).forEach((key) => {
          if (dto[key] !== null && dto[key] !== undefined) {
            formData.append(key, dto[key]);
          }
        });
        if (result.files && result.files.length > 0) {
          result.files.forEach((file) => {
            formData.append('attachments[]', file);
          });
        }

        let action$;
        if (mode === 'create') {
          action$ = this.vehicleService.createMaintenance(unitId, formData);
        } else {
          formData.append('_method', 'PUT');
          action$ = this.vehicleService.updateMaintenanceWithFiles(reportId!, formData);
        }

        action$.subscribe({
          next: () => {
            this.loadUnits();
            const msg = dto.status === 'draft' ? 'Borrador guardado' : 'Reporte generado con Ã©xito';
            this.snackBar.open(msg, 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
          },
          error: (err) => {
            console.error(err);
            this.snackBar.open(`Error: ${this.getFirstErrorMessage(err)}`, 'Cerrar', { duration: 5000, panelClass: 'error-snackbar' });
          },
        });
      }
    });
  }

  onDeleteReport(reportId: number, event?: MouseEvent): void {
    if (event) event.stopPropagation();
    Swal.fire({
      title: 'Â¿Eliminar Reporte?',
      text: 'Esta acciÃ³n no se puede deshacer. Se eliminarÃ¡ el historial.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (result.isConfirmed) {
        this.vehicleService.deleteMaintenance(reportId).subscribe({
          next: () => {
            this.loadUnits();
            Swal.fire('Eliminado', 'El reporte ha sido eliminado.', 'success');
          },
          error: () => {
            Swal.fire('Error', 'No se pudo eliminar el reporte.', 'error');
          },
        });
      }
    });
  }

  onDownloadReport(report: MaintenanceLog): void {
    if (!report) return;
    this.vehicleService.downloadMaintenancePdf(report.id).subscribe({
      next: (blob) => {
        const blobUrl = URL.createObjectURL(blob);
        window.open(blobUrl, '_blank');
        setTimeout(() => URL.revokeObjectURL(blobUrl), 10000);
      },
      error: () => {
        this.snackBar.open('No se pudo descargar el reporte.', 'Cerrar', { duration: 4000, panelClass: 'error-snackbar' });
      },
    });
  }

  onAddDocument(): void {
    const cost = this.newDocumentCost();
    const file = this.newDocumentFile();
    const unitId = this.selectedUnitId();
    if (!unitId || cost === null || cost < 0 || !file) return;

    this.isUploading.set(true);
    this.uploadAndAddDocument(unitId, cost, file);
    this.clearFileSelection();
    this.newDocumentCost.set(null);
    this.isUploading.set(false);
  }

  uploadAndAddDocument(unitId: number, cost: number, file: File): void {
    this.vehicleService.uploadDocument(unitId, cost, file).subscribe({
      next: () => {
        this.loadUnits();
        this.snackBar.open('Documento subido', 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
      },
    });
  }

  onDeleteDocument(docId: number): void {
    this.vehicleService.deleteDocument(docId).subscribe({ next: () => this.loadUnits() });
  }

  onTogglePayment(docId: number, event: MouseEvent): void {
    event.stopPropagation();
    this.allUnits.update((units) => {
      return units.map((unit) => {
        if (unit.id === this.selectedUnitId()) {
          return {
            ...unit,
            documents: unit.documents.map((doc) => {
              if (doc.id === docId) return { ...doc, is_paid: !doc.is_paid };
              return doc;
            }),
          };
        }
        return unit;
      });
    });

    this.vehicleService.toggleDocumentPayment(docId).subscribe({
      next: (updatedDoc) => {
        const statusMsg = updatedDoc.is_paid ? 'marcado como pagado' : 'marcado como pendiente';
        this.snackBar.open(`Documento ${statusMsg}`, 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
      },
      error: () => {
        this.snackBar.open('Error al actualizar estado. Revertiendo...', 'Cerrar', { duration: 3000, panelClass: 'error-snackbar' });
        this.loadUnits();
      },
    });
  }

  onDownloadDocument(url: string): void {
    if (url) window.open(url, '_blank');
  }

  clearFileSelection() {
    this.newDocumentName.set('');
    this.newDocumentFile.set(null);
    if (this.fileInputRef?.nativeElement) this.fileInputRef.nativeElement.value = '';
  }

  onFileSelected(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
      this.newDocumentName.set(input.files[0].name);
      this.newDocumentFile.set(input.files[0]);
    }
  }

  openCreateUnitDialog(): void {
    const isMobile = this.isMobile();
    const dialogRef = this.dialog.open(CreateFiretruckComponent, {
      width: isMobile ? '95vw' : '600px',
      maxWidth: '100vw',
      maxHeight: '95vh',
      autoFocus: false,
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result && result.formData && !result.id) {
        this.vehicleService.createUnit(result.formData, result.imageFile).subscribe({
          next: (newCar) => {
            this.allUnits.update((units) => [this.mapApiCarToVehicleUnit(newCar), ...units]);
            this.selectedUnitId.set(newCar.id);
            this.snackBar.open('Unidad creada correctamente', 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
          },
        });
      }
    });
  }

  createVendorReportLink(): void {
    const unit = this.selectedUnit();
    const email = this.vendorEmail().trim();
    const expiresInDays = this.vendorExpiresDays();
    const name = this.vendorName().trim();

    if (!unit) return;
    if (!email || !email.includes('@')) {
      this.snackBar.open('Ingresa un correo valido.', 'Cerrar', { duration: 4000, panelClass: 'error-snackbar' });
      return;
    }
    if (!expiresInDays || expiresInDays < 1) {
      this.snackBar.open('Los dias de expiracion deben ser mayor a 0.', 'Cerrar', { duration: 4000, panelClass: 'error-snackbar' });
      return;
    }

    this.isCreatingVendorLink.set(true);
    this.vendorLinkError.set(null);

    this.vehicleService
      .createVendorReportLink({
        email,
        name: name || null,
        car_id: unit.id,
        expires_in_days: expiresInDays,
      })
      .subscribe({
        next: (response) => {
          this.vendorLink.set(response.url);
          this.vendorLinkExpiresAt.set(response.expires_at);
          this.isCreatingVendorLink.set(false);
          this.snackBar.open('Link generado y enviado al proveedor.', 'Cerrar', { duration: 4000, panelClass: 'success-snackbar' });
        },
        error: (err) => {
          this.isCreatingVendorLink.set(false);
          const message = this.getFirstErrorMessage(err, 'No se pudo generar el link.');
          this.vendorLinkError.set(message);
          this.snackBar.open(message, 'Cerrar', { duration: 5000, panelClass: 'error-snackbar' });
        },
      });
  }

  copyVendorLink(): void {
    const link = this.vendorLink();
    if (!link) return;

    if (navigator?.clipboard?.writeText) {
      navigator.clipboard.writeText(link).then(() => {
        this.snackBar.open('Link copiado.', 'Cerrar', { duration: 3000, panelClass: 'success-snackbar' });
      });
      return;
    }

    this.snackBar.open('No se pudo copiar el link.', 'Cerrar', { duration: 3000, panelClass: 'error-snackbar' });
  }
}
