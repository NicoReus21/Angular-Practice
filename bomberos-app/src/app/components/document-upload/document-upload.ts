import { Component, computed, signal, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { of } from 'rxjs';
import { delay, finalize } from 'rxjs/operators';

// Material Design Modules
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatIconModule } from '@angular/material/icon';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';

// Services
import { FileUploadService } from '../../services/upload';
import { HistorialService, HistorialElement } from '../../services/historial';

// Interfaces
export interface UploadStep {
  title: string;
  optional: boolean;
  files: File[];
  isCompleted: boolean;
  isPaid?: boolean;
}

export interface UploadSection {
  title: string;
  steps: UploadStep[];
}

@Component({
  selector: 'app-document-upload',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule, MatToolbarModule, MatButtonModule,
    MatIconModule, MatExpansionModule, MatCheckboxModule, MatFormFieldModule,
    MatInputModule, MatSelectModule
  ],
  templateUrl: './document-upload.html',
  styleUrls: ['./document-upload.scss'],
})
export class DocumentUploadComponent implements OnInit {
  // State Signals
  editMode = signal(false);
  currentRecordId = signal<string | null>(null);
  preliminaryDataCompleted = signal(false);
  flatStepIndex = signal(-1);
  isUploading = signal(false);
  uploadError = signal<string | null>(null);

  // Form & Data
  preliminaryForm: FormGroup;
  fireCompanies = ['Primera', 'Segunda', 'Tercera', 'Quinta', 'Sexta', 'Séptima', 'Octava', 'Décima'];
  sections = signal<UploadSection[]>([]);

  // Dependency Injection
  private fb = inject(FormBuilder);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private uploadService = inject(FileUploadService);
  private historialService = inject(HistorialService);

  // Computed Signals
  allSteps = computed(() => this.sections().flatMap(s => s.steps));
  requiredStepsCompleted = computed(() => 
    this.allSteps()
      .filter(step => !step.optional)
      .every(step => step.isCompleted)
  );

  currentSectionIndex = computed(() => {
    const currentFlatIndex = this.flatStepIndex();
    if (currentFlatIndex < 0) return -1;
    let stepCount = 0;
    for (let i = 0; i < this.sections().length; i++) {
      stepCount += this.sections()[i].steps.length;
      if (currentFlatIndex < stepCount) return i;
    }
    return -1;
  });

  constructor() {
    this.preliminaryForm = this.fb.group({
      bomberoNombre: ['', Validators.required],
      compania: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.route.paramMap.subscribe(params => {
      const id = params.get('id');
      if (id) {
        this.setupEditMode(id);
      } else {
        this.setupCreateMode();
      }
    });
  }

  private setupEditMode(id: string): void {
    this.editMode.set(true);
    this.currentRecordId.set(id);
    const record = this.historialService.getRecordById(id);
    if (record && record.sections) {
      this.preliminaryForm.patchValue({
        bomberoNombre: record.nombre,
        compania: record.compania,
      });
      this.preliminaryForm.disable();
      this.sections.set(JSON.parse(JSON.stringify(record.sections)));
      this.preliminaryDataCompleted.set(true);
      this.flatStepIndex.set(-1); 
    }
  }

  private setupCreateMode(): void {
    this.editMode.set(false);
    this.preliminaryDataCompleted.set(false);
    this.flatStepIndex.set(-1);
    this.preliminaryForm.reset();
    this.preliminaryForm.enable();
    this.currentRecordId.set(null);
    this.isUploading.set(false);
    this.uploadError.set(null);
    this.sections.set(this.getInitialSections());
  }
  
  submitPreliminaryData() {
    if (this.preliminaryForm.valid) {
      this.preliminaryDataCompleted.set(true);
      this.startProcess();
    }
  }

  getFlatIndex(sectionIndex: number, stepIndex: number): number {
    let flatIndex = 0;
    for (let i = 0; i < sectionIndex; i++) {
      flatIndex += this.sections()[i].steps.length;
    }
    return flatIndex + stepIndex;
  }

  togglePaidStatus(step: UploadStep): void {
    step.isPaid = !step.isPaid;
    this.sections.update(sections => [...sections]);
  }

  startProcess(): void {
    this.flatStepIndex.set(0);
  }

  nextStep(): void {
    const currentIndex = this.flatStepIndex();
    const currentStep = this.allSteps()[currentIndex];

    if (currentStep.optional && currentStep.files.length === 0) {
        currentStep.isCompleted = true;
        this.sections.update(s => [...s]);
        this.advanceToNextStep();
        return;
    }

    this.isUploading.set(true);
    of(null).pipe(
      delay(500),
      finalize(() => this.isUploading.set(false))
    ).subscribe(() => {
      currentStep.isCompleted = true;
      this.sections.update(s => [...s]);
      this.advanceToNextStep();
    });
  }

  private advanceToNextStep(): void {
    const currentIndex = this.flatStepIndex();
    const totalSteps = this.allSteps().length;
    if (currentIndex < totalSteps - 1) {
      this.flatStepIndex.set(currentIndex + 1);
    }
  }

  handleFiles(fileList: FileList, globalIndex: number): void {
    this.allSteps()[globalIndex].files.push(...Array.from(fileList));
    this.sections.set([...this.sections()]);
  }
  
  removeFile(step: UploadStep, fileToRemove: File): void {
    step.files = step.files.filter(f => f !== fileToRemove);
  }

  finishProcess(): void {
    if (this.preliminaryForm.valid) {
      this.historialService.addRecord({
        nombre: this.preliminaryForm.value.bomberoNombre,
        compania: this.preliminaryForm.value.compania,
        sections: this.sections(),
      });
      this.flatStepIndex.set(this.allSteps().length);
      setTimeout(() => {
        this.router.navigate(['/historial']);
      }, 2000);
    }
  }
  
  saveChanges(): void {
    const id = this.currentRecordId();
    if (id) {
      const existingRecord = this.historialService.getRecordById(id);
      if (existingRecord) {
        this.historialService.updateRecord({
          ...existingRecord,
          sections: this.sections(),
        });
        this.router.navigate(['/historial']);
      }
    }
  }

  onDragOver(event: DragEvent) { event.preventDefault(); (event.currentTarget as HTMLElement).classList.add('drag-over'); }
  onDragLeave(event: DragEvent) { event.preventDefault(); (event.currentTarget as HTMLElement).classList.remove('drag-over'); }
  onDrop(event: DragEvent, globalIndex: number) {
    event.preventDefault();
    (event.currentTarget as HTMLElement).classList.remove('drag-over');
    if (event.dataTransfer?.files) {
      this.handleFiles(event.dataTransfer.files, globalIndex);
    }
  }

  private getInitialSections(): UploadSection[] {
    return [
        {
            title: 'REQUERIMIENTO OPERATIVO',
            steps: [
            { title: 'Reporte Flash', optional: false, files: [], isCompleted: false },
            { title: 'DIAB (declaracion Individual de accidente bomberil)', optional: false, files: [], isCompleted: false },
            { title: 'Informe del OBAC', optional: false, files: [], isCompleted: false },
            { title: 'Declaracion de testigos (si es que aplica)', optional: true, files: [], isCompleted: false },
            { title: 'Incidente sin lesiones Copia del Libro de Guardia (no legalizado y solo el el registro del accidente)', optional: true, files: [], isCompleted: false },
            ],
        },
        {
            title: 'ANTECEDENTES GENERALES',
            steps: [
            { title: 'Certificado de Carabineros.', optional: false, files: [], isCompleted: false },
            { title: 'DAU o variantes.', optional: false, files: [], isCompleted: false },
            { title: 'Orden de atención médica', optional: false, files: [], isCompleted: false },
            { title: 'Informe Médico emitido por médico tratante, con diagnóstico y prestaciones', optional: false, files: [], isCompleted: false },
            { title: 'Otros Documentos de caracter Médico adicional', optional: true, files: [], isCompleted: false },
            ],
        },
        {
            title: 'DOCUMENTOS DEL CUERPO DE BOMBEROS',
            steps: [
            { title: 'Certificado Superintendente que acredite calidad voluntario', optional: false, files: [], isCompleted: false },
            { title: 'Copia libro de guardia 3 días previos y 3 días posteriores al accidente Legalizado ante notario.', optional: false, files: [], isCompleted: false },
            { title: 'Copia libro de llamadas (central de Alarmas) referido a 3 días previos y posteriores al accidente autorizado ante notario.', optional: true, files: [], isCompleted: false },
            { title: 'Copia Aviso de citación al acto de servicio (ACADEMIA).', optional: false, files: [], isCompleted: false },
            { title: 'Copia Lista de Asistencia al acto de servicio específico (ACADEMIA), autorizado ante notario.', optional: false, files: [], isCompleted: false },
            { title: 'Informe Ejecutivo sobre el acto de servicio en que se producen lesiones, suscrito por Superintendente y Comandante.', optional: false, files: [], isCompleted: false },
            ],
        },
        {
            title: 'PRESTACIONES MEDICA',
            steps: [
            { title: 'Factura establecimiento hospitalario, con detalle de prestaciones.', optional: false, files: [], isCompleted: false, isPaid: false },
            { title: 'Boletas de Honorarios Profesionales, no incluidas en factura, visadas por medico jefe del servicio.', optional: true, files: [], isCompleted: false },
            { title: 'Boleta de Medicamentos y prescripción correspondiente.', optional: false, files: [], isCompleted: false },
            { title: 'Certificado del director del servicio que autoriza exámenes, recetas, medicamentos, controles, traslados, acciones médicas y procedimientos generales...', optional: true, files: [], isCompleted: false },
            ],
        },
        {
            title: 'GASTOS DE TRASLADOS Y ALIMENTACIÓN',
            steps: [
            { title: 'Boleta o factura de gastos de traslado del voluntario', optional: true, files: [], isCompleted: false },
            { title: 'Certificado del médico tratante que determine la incapacidad de asistir al voluntario...', optional: true, files: [], isCompleted: false },
            { title: 'Certificado médico tratante que justifique la necesidad de traslado del voluntario y del medio empleado.', optional: true, files: [], isCompleted: false },
            { title: 'Boleta de gastos de hospedaje del acompañante del voluntario.', optional: true, files: [], isCompleted: false },
            { title: 'Boleta de gastos de alimentación del acompañante del voluntario.', optional: true, files: [], isCompleted: false },
            { title: 'Otros (Especificar)', optional: true, files: [], isCompleted: false },
            ],
        },
    ];
  }
}

