import {
  Component,
  ChangeDetectionStrategy,
  inject,
  OnInit,
  signal,
  ViewChild,
  ElementRef,
  AfterViewInit,
  OnDestroy,
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
import { HttpClientModule } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { MachineHistorialService, ApiMaintenance } from '../../services/machine-historial';

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
    HttpClientModule,
  ],
  templateUrl: './create-report.html',
  styleUrls: ['./create-report.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()],
})
export class CreateReportComponent implements OnInit, AfterViewInit, OnDestroy {
  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateReportComponent>);
  private machineService = inject(MachineHistorialService);
  private backendUrl = environment.backendUrl;

  public data: {
    unit: { 
      id: number; 
      model: string | null; 
      plate: string; 
      company: string; 
      documents?: any[];
      manufacturing_year?: number; 
      chassis_number?: string;
      // Index signature para permitir acceso dinámico
      [key: string]: any; 
    };
    editMode?: boolean;
    reportData?: ApiMaintenance;
  } = inject(MAT_DIALOG_DATA);

  reportForm: FormGroup;
  selectedFiles = signal<File[]>([]);
  dialogTitle = 'Crear Nuevo Reporte';
  existingImages = signal<any[]>([]);
  showSavedInspectorSignature = signal(false);
  showSavedOfficerSignature = signal(false);

  @ViewChild('inspectorCanvas') inspectorCanvasRef!: ElementRef<HTMLCanvasElement>;
  @ViewChild('officerCanvas') officerCanvasRef!: ElementRef<HTMLCanvasElement>;

  private contexts: { [key: string]: CanvasRenderingContext2D | null } = {
    inspector: null,
    officer: null,
  };
  private isDrawing: { [key: string]: boolean } = { inspector: false, officer: false };
  private lastPos: { [key: string]: { x: number; y: number } } = {
    inspector: { x: 0, y: 0 },
    officer: { x: 0, y: 0 },
  };

  constructor() {
    this.reportForm = this.fb.group({
      plate: [{ value: '', disabled: true }],
      company: [{ value: '', disabled: true }],
      model: [{ value: '', disabled: true }],
      // CORRECCIÓN: Usamos snake_case para coincidir con el HTML y resolver el error
      manufacturing_year: [{ value: '', disabled: true }],
      chassis_number: [{ value: '', disabled: true }],
      
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
      const u = this.data.unit;

      this.reportForm.patchValue({
        model: u.model,
        plate: u.plate,
        company: u.company,
        // Asignamos a los controles en snake_case
        manufacturing_year: u.manufacturing_year,
        chassis_number: u.chassis_number,
      });
    }

    if (this.data.editMode && this.data.reportData) {
      this.dialogTitle = 'Editar Borrador';
      const r = this.data.reportData;
      let fecha = new Date();
      if (r.service_date)
        fecha = new Date(r.service_date + (r.service_date.includes('T') ? '' : 'T00:00:00'));

      this.reportForm.patchValue({
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

      if (r.inspector_signature && r.inspector_signature.length > 50)
        this.showSavedInspectorSignature.set(true);
      if (r.officer_signature && r.officer_signature.length > 50)
        this.showSavedOfficerSignature.set(true);

      if (r.documents && r.documents.length > 0) {
        const mappedDocs = r.documents.map((doc: any) => ({ ...doc, previewUrl: null }));
        this.existingImages.set(mappedDocs);
        this.loadExistingImages(mappedDocs);
      }
    }
  }

  ngAfterViewInit() {
    if (!this.showSavedInspectorSignature()) {
      this.initCanvas('inspector', this.inspectorCanvasRef?.nativeElement);
    }
    if (!this.showSavedOfficerSignature()) {
      this.initCanvas('officer', this.officerCanvasRef?.nativeElement);
    }
    window.addEventListener('resize', this.onResize);
  }

  ngOnDestroy() {
    window.removeEventListener('resize', this.onResize);
  }

  private onResize = () => {
    if (!this.showSavedInspectorSignature())
      this.initCanvas('inspector', this.inspectorCanvasRef?.nativeElement);
    if (!this.showSavedOfficerSignature())
      this.initCanvas('officer', this.officerCanvasRef?.nativeElement);
  };

  private initCanvas(id: 'inspector' | 'officer', canvas: HTMLCanvasElement) {
    if (!canvas) return;

    const parent = canvas.parentElement;
    if (parent) {
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      canvas.width = parent.clientWidth * ratio;
      canvas.height = parent.clientHeight * ratio;

      const ctx = canvas.getContext('2d')!;
      ctx.scale(ratio, ratio);
      ctx.lineWidth = 2.5;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = '#000000';

      this.contexts[id] = ctx;
    }

    const opts = { passive: false };
    const newCanvas = canvas.cloneNode(true) as HTMLCanvasElement;
    canvas.parentNode?.replaceChild(newCanvas, canvas);

    if (id === 'inspector') this.inspectorCanvasRef = new ElementRef(newCanvas);
    else this.officerCanvasRef = new ElementRef(newCanvas);

    const ctx = newCanvas.getContext('2d')!;
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    ctx.scale(ratio, ratio);
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    this.contexts[id] = ctx;

    // Mouse
    newCanvas.addEventListener('mousedown', (e) => this.startDrawing(id, e));
    newCanvas.addEventListener('mousemove', (e) => this.draw(id, e));
    newCanvas.addEventListener('mouseup', () => this.stopDrawing(id));
    newCanvas.addEventListener('mouseout', () => this.stopDrawing(id));

    // Touch
    newCanvas.addEventListener('touchstart', (e) => this.startDrawing(id, e), opts);
    newCanvas.addEventListener('touchmove', (e) => this.draw(id, e), opts);
    newCanvas.addEventListener('touchend', () => this.stopDrawing(id));
  }

  private getPos(e: any, canvas: HTMLCanvasElement) {
    const rect = canvas.getBoundingClientRect();
    let cx, cy;
    if (e.changedTouches && e.changedTouches.length > 0) {
      cx = e.changedTouches[0].clientX;
      cy = e.changedTouches[0].clientY;
    } else {
      cx = e.clientX;
      cy = e.clientY;
    }
    return { x: cx - rect.left, y: cy - rect.top };
  }

  private startDrawing(id: string, e: any) {
    if (e.type === 'touchstart') e.preventDefault(); 
    this.isDrawing[id] = true;

    const canvas =
      id === 'inspector'
        ? this.inspectorCanvasRef.nativeElement
        : this.officerCanvasRef.nativeElement;
    this.lastPos[id] = this.getPos(e, canvas);
  }

  private draw(id: string, e: any) {
    if (!this.isDrawing[id]) return;
    if (e.type === 'touchmove') e.preventDefault(); 

    const canvas =
      id === 'inspector'
        ? this.inspectorCanvasRef.nativeElement
        : this.officerCanvasRef.nativeElement;
    const ctx = this.contexts[id];
    const newPos = this.getPos(e, canvas);

    if (ctx) {
      ctx.beginPath();
      ctx.moveTo(this.lastPos[id].x, this.lastPos[id].y);
      ctx.lineTo(newPos.x, newPos.y);
      ctx.stroke();
    }
    this.lastPos[id] = newPos;
  }

  private stopDrawing(id: string) {
    if (this.isDrawing[id]) {
      this.isDrawing[id] = false;
      this.saveSignatureToForm(id);
    }
  }

  private saveSignatureToForm(id: string) {
    const canvas =
      id === 'inspector'
        ? this.inspectorCanvasRef?.nativeElement
        : this.officerCanvasRef?.nativeElement;
    if (canvas && !this.isCanvasBlank(canvas)) {
      const dataUrl = canvas.toDataURL('image/png');
      const fieldName = id === 'inspector' ? 'inspector_signature' : 'officer_signature';
      this.reportForm.patchValue({ [fieldName]: dataUrl });
    }
  }

  private isCanvasBlank(canvas: HTMLCanvasElement): boolean {
    const blank = document.createElement('canvas');
    blank.width = canvas.width;
    blank.height = canvas.height;
    return canvas.toDataURL() === blank.toDataURL();
  }


  clearInspectorSignature() {
    this.reportForm.patchValue({ inspector_signature: null });
    this.showSavedInspectorSignature.set(false);
    setTimeout(() => {
      if (this.inspectorCanvasRef?.nativeElement) {
        const canvas = this.inspectorCanvasRef.nativeElement;
        const ctx = this.contexts['inspector'];
        ctx?.clearRect(
          0,
          0,
          canvas.width / (window.devicePixelRatio || 1),
          canvas.height / (window.devicePixelRatio || 1)
        );

        this.initCanvas('inspector', canvas);
      }
    }, 50);
  }

  clearOfficerSignature() {
    this.reportForm.patchValue({ officer_signature: null });
    this.showSavedOfficerSignature.set(false);
    setTimeout(() => {
      if (this.officerCanvasRef?.nativeElement) {
        const canvas = this.officerCanvasRef.nativeElement;
        const ctx = this.contexts['officer'];
        ctx?.clearRect(
          0,
          0,
          canvas.width / (window.devicePixelRatio || 1),
          canvas.height / (window.devicePixelRatio || 1)
        );
        this.initCanvas('officer', canvas);
      }
    }, 50);
  }

  onSave(status: string): void {
    const formData = this.reportForm.getRawValue();

    if (status === 'completed') {
      const hasInspector = this.showSavedInspectorSignature() || !!formData.inspector_signature;
      const hasOfficer = this.showSavedOfficerSignature() || !!formData.officer_signature;

      if (!hasInspector) {
        alert('Falta firma inspector');
        return;
      }
      if (!hasOfficer) {
        alert('Falta firma oficial');
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

    // Remover campos de solo lectura antes de enviar
    delete formData.model;
    delete formData.plate;
    delete formData.company;
    delete formData.manufacturing_year; // Nombre actualizado a snake_case
    delete formData.chassis_number;     // Nombre actualizado a snake_case

    this.dialogRef.close({
      formData: { ...formData, status },
      files: this.selectedFiles(),
    });
  }

  private loadExistingImages(docs: any[]) {
    docs.forEach((doc) => {
      if (!doc?.id) return;
      this.machineService.downloadMaintenanceDocument(doc.id).subscribe({
        next: (blob) => {
          const url = URL.createObjectURL(blob);
          this.existingImages.update((current) =>
            current.map((item) => (item.id === doc.id ? { ...item, previewUrl: url } : item))
          );
        },
        error: () => console.error('Error cargando imagen'),
      });
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      const newFiles = Array.from(input.files).filter((file) => file.type.startsWith('image/'));
      this.selectedFiles.update((currentFiles) => [...currentFiles, ...newFiles]);
    }
  }

  onRemoveFile(fileToRemove: File): void {
    this.selectedFiles.update((currentFiles) =>
      currentFiles.filter((file) => file !== fileToRemove)
    );
  }

  onCancel(): void {
    this.dialogRef.close();
  }
}