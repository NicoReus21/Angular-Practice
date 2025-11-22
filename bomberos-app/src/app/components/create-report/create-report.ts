import { Component, ChangeDetectionStrategy, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, provideNativeDateAdapter } from '@angular/material/core';
import { MatDividerModule } from '@angular/material/divider';
import { MatListModule } from '@angular/material/list';
import { MatTooltipModule } from '@angular/material/tooltip'; // Importante para el matTooltip

// Importamos ApiMaintenance del servicio para los datos del reporte
import { ApiMaintenance } from '../../services/machine-historial';

@Component({
  selector: 'app-create-report',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatIconModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatDividerModule,
    MatListModule,
    MatTooltipModule
  ],
  templateUrl: './create-report.html',
  styleUrls: ['./create-report.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()]
})
export class CreateReportComponent implements OnInit {

  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateReportComponent>);
  
  // DEFINIMOS EL TIPO DE DATOS AQUÍ EN LÍNEA PARA EVITAR ERRORES DE IMPORTACIÓN
  public data: { 
    unit: { id: number; model: string | null; plate: string; company: string }; 
    editMode?: boolean; 
    reportData?: ApiMaintenance 
  } = inject(MAT_DIALOG_DATA);

  reportForm: FormGroup;
  selectedFiles = signal<File[]>([]);
  
  dialogTitle = 'Crear Nuevo Reporte'; // Variable para el título dinámico

  constructor() {
    this.reportForm = this.fb.group({
      plate: [{ value: '', disabled: true }],
      company: [{ value: '', disabled: true }],
      model: [{ value: '', disabled: true }], 
      
      chassis_number: [''], 
      mileage: ['', Validators.required], 
      cabin: [''], 
      filter_code: [''], 
      hourmeter: [''], 
      warnings: [''], 
      service_type: ['', Validators.required], 
      inspector_name: ['', Validators.required], 
      location: [''], 
      service_date: [new Date(), Validators.required], 
      reported_problem: ['', Validators.required], 
      activities_detail: ['', Validators.required], 
      pending_work: [''], 
      pending_type: ['Ninguno'], 
      observations: [''], 
      inspector_signature: ['', Validators.required], 
      officer_signature: ['', Validators.required], 
      car_info_annex: [''], 
    });
  }

  ngOnInit(): void {
    // 1. Cargar datos de la unidad
    if (this.data.unit) {
      this.reportForm.patchValue({
        model: this.data.unit.model,
        plate: this.data.unit.plate,
        company: this.data.unit.company,
      });
    }

    // 2. Cargar datos del BORRADOR (Si estamos editando)
    if (this.data.editMode && this.data.reportData) {
      this.dialogTitle = 'Editar Borrador de Reporte'; // Cambiar título
      const r = this.data.reportData;

      let fecha = new Date();
      if (r.service_date) {
         fecha = new Date(r.service_date + 'T00:00:00');
      }

      this.reportForm.patchValue({
        chassis_number: r.car_info_annex ? '' : '', // Ojo: chassis_number no está en ApiMaintenance por defecto, verifica tu API
        mileage: r.mileage,
        cabin: r.cabin,
        filter_code: r.filter_code,
        hourmeter: r.hourmeter,
        warnings: r.warnings,
        service_type: r.service_type,
        inspector_name: r.inspector_name,
        location: r.location,
        service_date: fecha,
        reported_problem: r.reported_problem,
        activities_detail: r.activities_detail,
        pending_work: r.pending_work,
        pending_type: r.pending_type || 'Ninguno',
        observations: r.observations,
        inspector_signature: r.inspector_signature,
        officer_signature: r.officer_signature,
        car_info_annex: r.car_info_annex
      });
    }
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(status: string): void {
    if (status === 'completed') {
      if (this.reportForm.invalid) {
        this.reportForm.markAllAsTouched();
        return;
      }
    } else {
      if (this.reportForm.get('service_date')?.invalid) {
        this.reportForm.get('service_date')?.markAsTouched();
        return;
      }
    }

    const formData = this.reportForm.getRawValue();
    delete formData.model;
    delete formData.plate;
    delete formData.company;

    const resultData = {
      ...formData,
      status: status
    };

    this.dialogRef.close({
      formData: resultData, 
      files: this.selectedFiles()
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      const newFiles = Array.from(input.files);
      this.selectedFiles.update(currentFiles => [...currentFiles, ...newFiles]);
    }
  }

  onRemoveFile(fileToRemove: File): void {
    this.selectedFiles.update(currentFiles => 
      currentFiles.filter(file => file !== fileToRemove)
    );
  }
}