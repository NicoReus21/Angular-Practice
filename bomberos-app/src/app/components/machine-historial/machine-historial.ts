import {
  Component,
  ChangeDetectionStrategy,
  signal,
  inject,
  computed,
  ViewChild,
  ElementRef,
} from '@angular/core';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { FormsModule } from '@angular/forms';

// Importaciones de Angular Material
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
import { MatToolbarModule } from '@angular/material/toolbar'; // <-- AÑADIDO

// Importaciones de tus componentes de diálogo
import { CreateFiretruckComponent } from '../create-firetruck/create-firetruck';
import { CreateReportComponent } from '../create-report/create-report';
import { CreateChecklistComponent } from '../create-checklist/create-checklist';

// --- Interfaces (Tipos de datos) ---
interface MaintenanceLog {
  id: string;
  date: string;
  technician: string;
  description: string;
}

interface ChecklistTaskItem {
  id: string;
  task: string;
  completed: boolean;
}

export interface ChecklistGroup {
  id: string;
  personaCargo: string;
  fechaRealizacion: string;
  items: ChecklistTaskItem[];
}

interface AttachedDocument {
  id: string;
  name: string;
  type: 'pdf' | 'doc' | 'img' | 'other';
  url: string;
  uploadedAt: string;
  cost?: number;
}

export interface VehicleUnit {
  id: string;
  name: string;
  status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio';
  model: string;
  plate: string;
  company: string;
  imageUrl: string;
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
    MatToolbarModule, // <-- AÑADIDO
  ],
  templateUrl: './machine-historial.html',
  styleUrls: ['./machine-historial.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class MachineHistorialComponent {
  private dialog = inject(MatDialog);

  @ViewChild('fileInput') fileInputRef!: ElementRef<HTMLInputElement>; // --- Datos de Ejemplo (Actualizados) ---

  private allUnits = signal<VehicleUnit[]>([
    {
      id: 'b6',
      name: 'Unidad B6',
      status: 'En Servicio',
      model: 'Iveco 160 E30',
      plate: 'KD-VV-46',
      company: '6ta-10ma',
      imageUrl: 'https://placehold.co/600x400/3498db/white?text=Unidad+B6&font=roboto',
      checklists: [
        {
          id: 'cl1',
          personaCargo: 'Juan Pérez',
          fechaRealizacion: '10-10-2025',
          items: [
            { id: 'c1', task: 'Revisar niveles de aceite', completed: true },
            { id: 'c2', task: 'Verificar presión de neumáticos', completed: true },
            { id: 'c3', task: 'Inspección de luces y sirenas', completed: false },
          ],
        },
        {
          id: 'cl2',
          personaCargo: 'Ana Gómez',
          fechaRealizacion: '01-09-2025',
          items: [
            { id: 'c4', task: 'Limpieza de cabina', completed: true },
            { id: 'c5', task: 'Revisión nivel agua estanque', completed: true },
          ],
        },
      ],
      documents: [
        {
          id: 'd1',
          name: 'Manual_Iveco_E30.pdf',
          type: 'pdf',
          url: '#',
          uploadedAt: '15-01-2025',
          cost: 0,
        },
        {
          id: 'd2',
          name: 'Factura_Taller_Mecánico.pdf',
          type: 'pdf',
          url: '#',
          uploadedAt: '05-10-2025',
          cost: 175500,
        },
        {
          id: 'd3',
          name: 'Repuesto_Bomba_Agua.png',
          type: 'img',
          url: '#',
          uploadedAt: '02-10-2025',
          cost: 89990,
        },
      ],
      maintenanceHistory: [
        {
          id: 'm1',
          date: '10-10-2025',
          technician: 'Juan Pérez',
          description: 'Cambio de aceite y filtros.',
        },
        {
          id: 'm2',
          date: '05-09-2025',
          technician: 'Ana Gómez',
          description: 'Revisión sistema de frenos.',
        },
      ],
    },
    {
      id: 'm1',
      name: 'Unidad M1',
      status: 'En Taller',
      model: 'Mercedes-Benz Atego',
      plate: 'BC-LR-88',
      company: '1era-5ta',
      imageUrl: 'https://placehold.co/600x400/e74c3c/white?text=Unidad+M1&font=roboto',
      checklists: [],
      documents: [],
      maintenanceHistory: [
        {
          id: 'm3',
          date: '15-10-2025',
          technician: 'Taller Municipal',
          description: 'Reparación de motor principal.',
        },
      ],
    },
    // Unidad de prueba para el filtro "Fuera de Servicio"
    {
      id: 'r3',
      name: 'Unidad R3',
      status: 'Fuera de Servicio',
      model: 'Renault D14',
      plate: 'XX-YY-ZZ',
      company: '3ra',
      imageUrl: 'https://placehold.co/600x400/95a5a6/white?text=Unidad+R3&font=roboto',
      checklists: [],
      documents: [],
      maintenanceHistory: [],
    },
  ]); // --- Signals (Estado del componente) ---

  selectedUnitId = signal<string | null>(this.allUnits()[0]?.id || null);
  currentStatusFilter = signal<'Todos' | 'En Servicio' | 'En Taller' | 'Fuera de Servicio'>(
    'Todos'
  );

  // ✅ AÑADIDO: Signal para controlar el Sidenav
  isSidenavOpen = signal(true); // Calcula la lista de unidades filtradas

  filteredUnits = computed(() => {
    const status = this.currentStatusFilter();
    if (status === 'Todos') {
      return this.allUnits();
    }
    return this.allUnits().filter((unit) => unit.status === status);
  }); // Calcula la unidad seleccionada actualmente

  selectedUnit = computed(() => {
    const id = this.selectedUnitId();
    return this.allUnits().find((u) => u.id === id) || null;
  }); // --- Signals para el formulario de documentos ---

  newDocumentName = signal<string>('');
  newDocumentCost = signal<number | null>(null); // --- Computed Signal para calcular el total de gastos ---

  totalDocumentsCost = computed(() => {
    const unit = this.selectedUnit();
    if (!unit || !unit.documents) {
      return 0;
    }
    return unit.documents.reduce((sum, doc) => sum + (doc.cost || 0), 0);
  }); // --- Métodos del Componente ---

  // ✅ AÑADIDO: Método para abrir/cerrar el Sidenav
  toggleSidenav(): void {
    this.isSidenavOpen.update((v) => !v);
  }

  onFilterChange(status: 'Todos' | 'En Servicio' | 'En Taller' | 'Fuera de Servicio'): void {
    this.currentStatusFilter.set(status);
  }

  onSelectUnit(id: string): void {
    this.selectedUnitId.set(id);
  }

  getStatusChipClass(status: 'En Servicio' | 'En Taller' | 'Fuera de Servicio'): string {
    switch (status) {
      case 'En Servicio':
        return 'status-chip-servicio';
      case 'En Taller':
        return 'status-chip-taller';
      case 'Fuera de Servicio':
        return 'status-chip-fuera';
    }
  }

  getDocumentIcon(type: 'pdf' | 'doc' | 'img' | 'other'): string {
    switch (type) {
      case 'pdf':
        return 'picture_as_pdf';
      case 'doc':
        return 'description';
      case 'img':
        return 'image';
      case 'other':
        return 'attach_file';
    }
  }

  onChecklistItemToggle(checklistGroupId: string, taskId: string): void {
    this.allUnits.update((units) => {
      const unit = units.find((u) => u.id === this.selectedUnitId());
      if (unit) {
        const checklistGroup = unit.checklists.find((cl) => cl.id === checklistGroupId);
        if (checklistGroup) {
          const taskItem = checklistGroup.items.find((item) => item.id === taskId);
          if (taskItem) {
            taskItem.completed = !taskItem.completed;
            console.log(`Checklist item ${taskId} cambiado a: ${taskItem.completed}`);
          }
        }
      }
      return [...units];
    });
  }

  onEditChecklist(checklistId: string, event: MouseEvent): void {
    event.stopPropagation();
    const unit = this.selectedUnit();
    if (!unit) return;

    const checklistToEdit = unit.checklists.find((cl) => cl.id === checklistId);
    if (!checklistToEdit) return;

    console.log('Intentando editar checklist (requiere lógica de diálogo):', checklistToEdit);
    console.warn(
      `Funcionalidad "Editar" (ID: ${checklistId}). Se necesita modificar el diálogo 'CreateChecklistComponent' para poblarlo con datos existentes.`
    ); /*
    try {
      const dialogRef = this.dialog.open(CreateChecklistComponent, {
        width: '600px',
        autoFocus: false,
        data: { isEdit: true, checklistData: JSON.parse(JSON.stringify(checklistToEdit)) }
      });

      dialogRef.afterClosed().subscribe(result => {
        if (result) {
          console.log('Checklist editado:', result);
          this.allUnits.update(units => {
            const unitToUpdate = units.find(u => u.id === unit.id);
            if (unitToUpdate) {
              const index = unitToUpdate.checklists.findIndex(cl => cl.id === checklistId);
              if (index > -1) {
                // ... (lógica para actualizar el checklist en el array) ...
              }
            }
            return [...units];
          });
        }
      });
    } catch (e) {
      console.error("Error al abrir CreateChecklistComponent para editar.", e);
    }
    */
  }

  onDeleteChecklist(checklistId: string, event: MouseEvent): void {
    event.stopPropagation();

    console.log('Eliminando checklist:', checklistId);
    const unitId = this.selectedUnitId();
    if (!unitId) return;

    this.allUnits.update((units) => {
      return units.map((unit) => {
        if (unit.id !== unitId) {
          return unit;
        }
        return {
          ...unit,
          checklists: unit.checklists.filter((cl) => cl.id !== checklistId),
        };
      });
    });
  }

  openCreateUnitDialog(): void {
    try {
      const dialogRef = this.dialog.open(CreateFiretruckComponent, {
        width: '500px',
        autoFocus: false,
        data: {},
      });

      dialogRef.afterClosed().subscribe((result) => {
        if (result) {
          console.log('Nueva unidad creada:', result);
          const newUnit: VehicleUnit = {
            id: `unit-${Math.random().toString(36).substring(2, 9)}`,
            name: result.name,
            status: result.status,
            model: result.model,
            plate: result.plate,
            company: result.company,
            imageUrl:
              result.imageUrl ||
              `https://placehold.co/600x400/cccccc/white?text=${result.name}&font=roboto`,
            checklists: [],
            documents: [],
            maintenanceHistory: [],
          };
          this.allUnits.update((units) => [newUnit, ...units]);
        }
      });
    } catch (e) {
      console.error(
        'Error al abrir CreateFiretruckComponent. Asegúrate de que esté importado y sea un componente.',
        e
      );
    }
  }

  openCreateReportDialog(): void {
    const unit = this.selectedUnit();
    if (!unit) return;

    try {
      const dialogRef = this.dialog.open(CreateReportComponent, {
        width: '800px',
        autoFocus: false,
        data: { unit: { ...unit } },
      });

      dialogRef.afterClosed().subscribe((result) => {
        if (result) {
          console.log('Nuevo reporte creado:', result);
          const newLog: MaintenanceLog = {
            id: `log-${Math.random().toString(36).substring(2, 9)}`,
            date: new Date(result.formData.fechaRealizacion).toLocaleDateString('es-CL'),
            technician: result.formData.inspectorCargo,
            description: result.formData.problemaReportado,
          };

          this.allUnits.update((units) => {
            const unitToUpdate = units.find((u) => u.id === this.selectedUnitId());
            if (unitToUpdate) {
              unitToUpdate.maintenanceHistory.unshift(newLog);
            }
            return [...units];
          });
        }
      });
    } catch (e) {
      console.error('Error al abrir CreateReportComponent.', e);
    }
  }

  openCreateChecklistDialog(): void {
    const unit = this.selectedUnit();
    if (!unit) return;

    try {
      const dialogRef = this.dialog.open(CreateChecklistComponent, {
        width: '600px',
        autoFocus: false,
      });

      dialogRef.afterClosed().subscribe((result) => {
        if (result) {
          console.log('Nuevo checklist creado:', result);

          const newChecklistGroup: ChecklistGroup = {
            id: `cl-${Math.random().toString(36).substring(2, 9)}`,
            personaCargo: result.formData.personaCargo,
            fechaRealizacion: new Date(result.formData.fechaRealizacion).toLocaleDateString(
              'es-CL'
            ),
            items: result.tasks.map((task: any) => ({
              ...task,
              id: `task-${Math.random().toString(36).substring(2, 9)}`,
              completed: false,
            })),
          };

          this.allUnits.update((units) => {
            const unitToUpdate = units.find((u) => u.id === this.selectedUnitId());
            if (unitToUpdate) {
              unitToUpdate.checklists.unshift(newChecklistGroup);
            }
            return [...units];
          });
        }
      });
    } catch (e) {
      console.error('Error al abrir CreateChecklistComponent.', e);
    }
  } // --- Métodos para la pestaña de Documentos ---

  clearFileSelection(): void {
    this.newDocumentName.set('');
    if (this.fileInputRef && this.fileInputRef.nativeElement) {
      this.fileInputRef.nativeElement.value = '';
    }
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.newDocumentName.set(input.files[0].name);
    } else {
      this.newDocumentName.set('');
    }
  }

  onAddDocument(): void {
    const cost = this.newDocumentCost();
    const name = this.newDocumentName();
    const unitId = this.selectedUnitId();

    if (!unitId || cost === null || cost < 0 || !name) {
      console.warn('Faltan datos para añadir el documento (nombre o costo no válido).');
      return;
    }

    let fileType: 'pdf' | 'doc' | 'img' | 'other' = 'other';
    if (name.endsWith('.pdf')) fileType = 'pdf';
    else if (name.endsWith('.doc') || name.endsWith('.docx')) fileType = 'doc';
    else if (name.endsWith('.png') || name.endsWith('.jpg') || name.endsWith('.jpeg'))
      fileType = 'img';

    const newDoc: AttachedDocument = {
      id: `doc-${Math.random().toString(36).substring(2, 9)}`,
      name: name,
      type: fileType,
      url: '#',
      uploadedAt: new Date().toLocaleDateString('es-CL'),
      cost: cost,
    };

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
  }

  onDeleteDocument(docId: string): void {
    const unitId = this.selectedUnitId();
    if (!unitId) return;

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
    console.log(`Documento ${docId} eliminado.`);
  }
}
