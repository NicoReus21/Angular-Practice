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
    if (this.data.unit) {
      // Actualizamos los campos al parchear
      this.reportForm.patchValue({
        model: this.data.unit.model,
        plate: this.data.unit.plate,
        company: this.data.unit.company,
      });
    }
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    if (this.reportForm.valid) {
      const formData = this.reportForm.getRawValue();
      
      delete formData.model;
      delete formData.plate;
      delete formData.company;

      this.dialogRef.close({
        formData: formData, 
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