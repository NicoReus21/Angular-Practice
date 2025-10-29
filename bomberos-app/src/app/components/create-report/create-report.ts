import { Component, ChangeDetectionStrategy, inject, OnInit, signal } from '@angular/core'; 
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

// Importaciones de Angular Material para el formulario
import { MatDialogModule, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDatepickerModule } from '@angular/material/datepicker';
// ✅ Importación corregida para incluir el 'provider'
import { MatNativeDateModule, provideNativeDateAdapter } from '@angular/material/core';
import { MatDividerModule } from '@angular/material/divider';
import { MatListModule } from '@angular/material/list'; 
import { VehicleUnit } from '../machine-historial/machine-historial'; 

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
  ],
  templateUrl: './create-report.html',
  styleUrls: ['./create-report.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [
    provideNativeDateAdapter()
  ]
})
export class CreateReportComponent implements OnInit {

  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateReportComponent>);
  
  public data: { unit: VehicleUnit } = inject(MAT_DIALOG_DATA);

  reportForm: FormGroup;

  selectedFiles = signal<File[]>([]);

  constructor() {
    this.reportForm = this.fb.group({

      marca: [{ value: '', disabled: true }],
      patente: [{ value: '', disabled: true }],
      compania: [{ value: '', disabled: true }],
      numeroChasis: [''],
    
      cabina: [''],
      filtrosCodigo: [''],
      horometro: [''],
      senalesAdvertencia: [''],
      kilometraje: ['', Validators.required],
      tipoServicio: ['', Validators.required],
      inspectorCargo: ['', Validators.required],
      ubicacionEquipo: [''],
      fechaRealizacion: [new Date(), Validators.required],
      problemaReportado: ['', Validators.required],
      detalleActividades: ['', Validators.required],
      trabajoPendiente: [''],
      tipoPendiente: ['Ninguno'],
      observacionesComplementarias: [''],
      firmaInspector: ['', Validators.required],
      firmaOficial: ['', Validators.required],
      anexoInfoCarro: [''],
    });
  }

  ngOnInit(): void {
    if (this.data.unit) {
      this.reportForm.patchValue({
        marca: this.data.unit.model,
        patente: this.data.unit.plate,
        compania: this.data.unit.company,
      });
    }
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    if (this.reportForm.valid) {
      this.dialogRef.close({
        formData: this.reportForm.getRawValue(), 
        files: this.selectedFiles()
      });
    } else {
      this.reportForm.markAllAsTouched();
    }
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

