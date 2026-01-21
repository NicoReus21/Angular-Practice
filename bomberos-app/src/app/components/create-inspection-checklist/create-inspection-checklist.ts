import { Component, ChangeDetectionStrategy, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormGroup,
  Validators,
  ReactiveFormsModule,
  FormsModule,
} from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { provideNativeDateAdapter } from '@angular/material/core';
import { MatDividerModule } from '@angular/material/divider';
import { MatButtonToggleModule } from '@angular/material/button-toggle';
import { MachineHistorialService, InspectionCategory } from '../../services/machine-historial';

type InspectionValue = 'yes' | 'no' | 'na';

interface InspectionItem {
  categoryId?: number;
  key: string;
  label: string;
  value: InspectionValue;
  comment: string;
}

@Component({
  selector: 'app-create-inspection-checklist',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatDatepickerModule,
    MatDividerModule,
    MatButtonToggleModule,
  ],
  templateUrl: './create-inspection-checklist.html',
  styleUrls: ['./create-inspection-checklist.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()],
})
export class CreateInspectionChecklistComponent {
  private fb = inject(FormBuilder);
  private vehicleService = inject(MachineHistorialService);
  public dialogRef = inject(MatDialogRef<CreateInspectionChecklistComponent>);
  public data = inject(MAT_DIALOG_DATA, { optional: true }) as any;

  form: FormGroup;
  items = signal<InspectionItem[]>([]);

  constructor() {
    this.form = this.fb.group({
      inspectedAt: [new Date(), Validators.required],
    });

    this.loadCategories();
  }

  private loadCategories(): void {
    this.vehicleService.getInspectionCategories().subscribe({
      next: (categories) => {
        if (!categories.length) {
          this.items.set(this.defaultItems());
          return;
        }
        const sorted = [...categories].sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        this.items.set(this.mapCategoriesToItems(sorted));
      },
      error: () => {
        this.items.set(this.defaultItems());
      },
    });
  }

  private mapCategoriesToItems(categories: InspectionCategory[]): InspectionItem[] {
    return categories.map((category) => ({
      categoryId: category.id,
      key: category.key,
      label: category.label,
      value: 'na',
      comment: '',
    }));
  }

  private defaultItems(): InspectionItem[] {
    return [
      { key: 'bodywork_check', label: 'Chequeo carroceria', value: 'na', comment: '' },
      { key: 'traffic_lights_check', label: 'Chequeo de luces de trafico', value: 'na', comment: '' },
      { key: 'emergency_lights_check', label: 'Chequeo de luces de emergencia', value: 'na', comment: '' },
      { key: 'air_pressure_loss_check', label: 'Chequeo perdida de presion sistema de aire', value: 'na', comment: '' },
      { key: 'windshield_check', label: 'Chequeo de parabrisas', value: 'na', comment: '' },
      { key: 'mirrors_check', label: 'Chequeo de retrovisores', value: 'na', comment: '' },
      { key: 'tires_check', label: 'Chequeo de neumaticos', value: 'na', comment: '' },
      { key: 'steps_check', label: 'Chequeo de pisaderas', value: 'na', comment: '' },
      { key: 'alternator_load', label: 'Carga de alternador', value: 'na', comment: '' },
      { key: 'batteries_visual_inspection', label: 'Baterias (inspeccion visual)', value: 'na', comment: '' },
      { key: 'tools_secured_top', label: 'Herramientas amarradas en parte superior', value: 'na', comment: '' },
      { key: 'fuel_level_over_three_quarters', label: 'Nivel de combustible sobre 3/4', value: 'na', comment: '' },
      { key: 'engine_oil_level', label: 'Nivel de aceite de motor', value: 'na', comment: '' },
      { key: 'engine_coolant_level', label: 'Nivel de refrigerante de motor', value: 'na', comment: '' },
      { key: 'steering_oil_level', label: 'Nivel de aceite de direccion', value: 'na', comment: '' },
      { key: 'wiper_blades_condition', label: 'Estado de plumillas limpia parabrisas', value: 'na', comment: '' },
      { key: 'adblue_level_over_25', label: 'Nivel de AdBlue sobre 25%', value: 'na', comment: '' },
    ];
  }

  setValue(item: InspectionItem, value: InspectionValue) {
    this.items.update((current) =>
      current.map((i) => (i.key === item.key ? { ...i, value } : i))
    );
  }

  setComment(item: InspectionItem, comment: string) {
    this.items.update((current) =>
      current.map((i) => (i.key === item.key ? { ...i, comment } : i))
    );
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const payloadItems = this.items().map((item) => ({
      category_id: item.categoryId ?? null,
      key: item.key,
      value: item.value,
      comment: item.value === 'no' ? item.comment?.trim() || '' : null,
    }));

    const missingComments = payloadItems.some((item) => item.value === 'no' && !item.comment);
    if (missingComments) {
      return;
    }

    this.dialogRef.close({
      formData: this.form.value,
      items: payloadItems,
    });
  }
}
