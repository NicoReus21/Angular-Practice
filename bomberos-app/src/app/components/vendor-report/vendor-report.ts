import {
  Component,
  ChangeDetectionStrategy,
  inject,
  OnInit,
  AfterViewInit,
  signal,
  ViewChild,
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, provideNativeDateAdapter } from '@angular/material/core';
import { MatDividerModule } from '@angular/material/divider';
import { MatCardModule } from '@angular/material/card';
import { MatListModule } from '@angular/material/list';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import {
  AngularSignaturePadModule,
  NgSignaturePadOptions,
  SignaturePadComponent,
} from '@almothafar/angular-signature-pad';
import { MachineHistorialService } from '../../services/machine-historial';

interface VendorReportLinkData {
  vendor: { name?: string | null; email: string };
  car: { id: number; name: string; plate: string; model: string | null; company: string };
  expires_at: string;
}

@Component({
  selector: 'app-vendor-report',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatIconModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatDividerModule,
    MatCardModule,
    MatListModule,
    MatSnackBarModule,
    AngularSignaturePadModule,
  ],
  templateUrl: './vendor-report.html',
  styleUrls: ['./vendor-report.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()],
})
export class VendorReportComponent implements OnInit, AfterViewInit {
  private fb = inject(FormBuilder);
  private route = inject(ActivatedRoute);
  private service = inject(MachineHistorialService);
  private snackBar = inject(MatSnackBar);

  reportForm: FormGroup;
  token = signal<string | null>(null);
  loading = signal(true);
  errorMessage = signal<string | null>(null);
  linkData = signal<VendorReportLinkData | null>(null);
  submitted = signal(false);
  isSubmitting = signal(false);
  selectedFiles = signal<File[]>([]);

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
      car_info_annex: [''],
      inspector_signature: [''],
      officer_signature: [''],
    });
  }

  ngOnInit(): void {
    const token = this.route.snapshot.paramMap.get('token');
    this.token.set(token);

    if (!token) {
      this.loading.set(false);
      this.errorMessage.set('Link invalido.');
      return;
    }

    this.loadLink(token);
  }

  ngAfterViewInit(): void {
    this.inspectorPad?.set('canvasWidth', 300);
    this.inspectorPad?.clear();
    this.officerPad?.clear();
  }

  loadLink(token: string): void {
    this.loading.set(true);
    this.errorMessage.set(null);

    this.service.getVendorReportLink(token).subscribe({
      next: (response: VendorReportLinkData) => {
        this.linkData.set(response);
        this.loading.set(false);
      },
      error: (err) => {
        this.loading.set(false);
        const message = err?.error?.message || 'No se pudo cargar el link.';
        if (err?.status === 404) {
          this.errorMessage.set('Link no encontrado.');
        } else if (err?.status === 410) {
          this.errorMessage.set(message);
        } else {
          this.errorMessage.set(message);
        }
      },
    });
  }

  clearInspectorSignature(): void {
    this.inspectorPad?.clear();
    this.reportForm.patchValue({ inspector_signature: null });
  }

  clearOfficerSignature(): void {
    this.officerPad?.clear();
    this.reportForm.patchValue({ officer_signature: null });
  }

  drawComplete(padName: 'inspector' | 'officer'): void {
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

  submitReport(): void {
    if (this.reportForm.invalid) {
      this.reportForm.markAllAsTouched();
      return;
    }

    const hasInspectorSig = this.inspectorPad && !this.inspectorPad.isEmpty();
    const hasOfficerSig = this.officerPad && !this.officerPad.isEmpty();

    if (!hasInspectorSig || !hasOfficerSig) {
      this.snackBar.open('Faltan firmas obligatorias.', 'Cerrar', { duration: 4000 });
      return;
    }

    const token = this.token();
    if (!token) return;

    const payload = this.reportForm.getRawValue();
    if (payload.service_date instanceof Date) {
      payload.service_date = payload.service_date.toISOString().split('T')[0];
    }

    payload.inspector_signature = this.inspectorPad.toDataURL();
    payload.officer_signature = this.officerPad.toDataURL();

    const formData = new FormData();
    Object.keys(payload).forEach((key) => {
      if (payload[key] !== null && payload[key] !== undefined) {
        formData.append(key, payload[key]);
      }
    });

    if (this.selectedFiles().length > 0) {
      this.selectedFiles().forEach((file) => {
        formData.append('attachments[]', file, file.name);
      });
    }

    this.isSubmitting.set(true);
    this.service.submitVendorReport(token, formData).subscribe({
      next: () => {
        this.isSubmitting.set(false);
        this.submitted.set(true);
        this.snackBar.open('Reporte enviado correctamente.', 'Cerrar', { duration: 4000 });
      },
      error: (err) => {
        this.isSubmitting.set(false);
        const message = err?.error?.message || 'No se pudo enviar el reporte.';
        this.snackBar.open(message, 'Cerrar', { duration: 5000 });
      },
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      const newFiles = Array.from(input.files).filter((file) => file.type.startsWith('image/'));
      if (newFiles.length < input.files.length) {
        this.snackBar.open('Solo se permiten imagenes.', 'Cerrar', { duration: 4000 });
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
