import {
  Component,
  ChangeDetectionStrategy,
  inject,
  OnInit,
  signal,
  ViewChild,
  AfterViewInit,
} from '@angular/core';
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
import { MatTooltipModule } from '@angular/material/tooltip';
import { ApiMaintenance } from '../../services/machine-historial';

import {
  AngularSignaturePadModule,
  NgSignaturePadOptions,
  SignaturePadComponent,
} from '@almothafar/angular-signature-pad';

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
    MatTooltipModule,
    AngularSignaturePadModule,
  ],
  templateUrl: './create-report.html',
  styleUrls: ['./create-report.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()],
})
export class CreateReportComponent implements OnInit, AfterViewInit {
  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateReportComponent>);

  public data: {
    unit: { id: number; model: string | null; plate: string; company: string; documents?: any[] };
    editMode?: boolean;
    reportData?: ApiMaintenance;
  } = inject(MAT_DIALOG_DATA);

  reportForm: FormGroup;
  selectedFiles = signal<File[]>([]);
  dialogTitle = 'Crear Nuevo Reporte';

  existingImages = signal<any[]>([]);
  showSavedInspectorSignature = signal(false);
  showSavedOfficerSignature = signal(false);

  @ViewChild('inspectorPad') inspectorPad!: SignaturePadComponent;
  @ViewChild('officerPad') officerPad!: SignaturePadComponent;

  public signaturePadOptions: NgSignaturePadOptions = {
    minWidth: 1,
    canvasWidth: 500,
    canvasHeight: 150,
    penColor: 'black',
    backgroundColor: 'white',
    dotSize: 1,
  };

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
      inspector_signature: [''],
      officer_signature: [''],
      car_info_annex: [''],
    });
  }

  ngOnInit(): void {
    if (this.data.unit) {
      this.reportForm.patchValue({
        model: this.data.unit.model,
        plate: this.data.unit.plate,
        company: this.data.unit.company,
      });
    }

    if (this.data.editMode && this.data.reportData) {
      this.dialogTitle = 'Editar Borrador';
      const r = this.data.reportData;

      let fecha = new Date();
      if (r.service_date)
        fecha = new Date(r.service_date + (r.service_date.includes('T') ? '' : 'T00:00:00'));

      this.reportForm.patchValue({
        chassis_number: r.chassis_number || '',
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
        car_info_annex: r.car_info_annex,
        inspector_signature: r.inspector_signature,
        officer_signature: r.officer_signature,
      });

      if (r.inspector_signature && r.inspector_signature.length > 50) {
        this.showSavedInspectorSignature.set(true);
      }
      if (r.officer_signature && r.officer_signature.length > 50) {
        this.showSavedOfficerSignature.set(true);
      }

      const unitDocs = this.data.unit.documents || [];
      const maintenanceDocs = unitDocs.filter(
        (doc: any) => doc.maintenance_id === r.id && (doc.type === 'img' || doc.file_type === 'img')
      );
      this.existingImages.set(maintenanceDocs);
    }
  }

  ngAfterViewInit() {
    this.inspectorPad?.set('canvasWidth', 300);
    this.inspectorPad?.clear();
    this.officerPad?.clear();
  }

  clearInspectorSignature() {
    this.inspectorPad?.clear();
    this.reportForm.patchValue({ inspector_signature: null });
    this.showSavedInspectorSignature.set(false);
  }

  clearOfficerSignature() {
    this.officerPad?.clear();
    this.reportForm.patchValue({ officer_signature: null });
    this.showSavedOfficerSignature.set(false);
  }

  drawComplete(padName: 'inspector' | 'officer') {
    if (padName === 'inspector') {
      if (this.inspectorPad && !this.inspectorPad.isEmpty()) {
        this.reportForm.patchValue({ inspector_signature: this.inspectorPad.toDataURL() });
      }
    } else {
      if (this.officerPad && !this.officerPad.isEmpty()) {
        this.reportForm.patchValue({ officer_signature: this.officerPad.toDataURL() });
      }
    }
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(status: string): void {
    if (status === 'completed') {
      const hasInspectorSig =
        this.showSavedInspectorSignature() || (this.inspectorPad && !this.inspectorPad.isEmpty());
      const hasOfficerSig =
        this.showSavedOfficerSignature() || (this.officerPad && !this.officerPad.isEmpty());

      if (!hasInspectorSig) {
        alert('Falta la firma del Inspector');
        return;
      }
      if (!hasOfficerSig) {
        alert('Falta la firma del Oficial');
        return;
      }

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

    if (!this.showSavedInspectorSignature()) {
      if (this.inspectorPad && !this.inspectorPad.isEmpty()) {
        formData.inspector_signature = this.inspectorPad.toDataURL();
      } else {
        formData.inspector_signature = null;
      }
    }

    if (!this.showSavedOfficerSignature()) {
      if (this.officerPad && !this.officerPad.isEmpty()) {
        formData.officer_signature = this.officerPad.toDataURL();
      } else {
        formData.officer_signature = null;
      }
    }

    delete formData.model;
    delete formData.plate;
    delete formData.company;

    const resultData = {
      ...formData,
      status: status,
    };

    this.dialogRef.close({
      formData: resultData,
      files: this.selectedFiles(),
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      const newFiles = Array.from(input.files).filter((file) => file.type.startsWith('image/'));
      if (newFiles.length < input.files.length) {
        alert('Solo se permiten imágenes. Otros archivos fueron descartados.');
      }
      this.selectedFiles.update((currentFiles) => [...currentFiles, ...newFiles]);
    }
  }

  onRemoveFile(fileToRemove: File): void {
    this.selectedFiles.update((currentFiles) =>
      currentFiles.filter((file) => file !== fileToRemove)
    );
  }
}
