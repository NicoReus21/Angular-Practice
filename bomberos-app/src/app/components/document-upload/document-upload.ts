import { Component, computed, signal, inject, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { finalize } from 'rxjs/operators'; 

import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatIconModule } from '@angular/material/icon';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';


import { FileUploadService } from '../../services/upload';
import { HistorialService } from '../../services/historial';

export interface ServerFile {
  id: number;
  file_name: string;
  file_path: string;
  fromServer: true;
  name: string;
}

export interface UploadStep {
  title: string;
  optional: boolean;
  files: (File | ServerFile)[];
  isCompleted: boolean;
  isPaid?: boolean;
  isUploading?: boolean;
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
    MatInputModule, MatSelectModule, MatProgressSpinnerModule
  ],
  templateUrl: './document-upload.html',
  styleUrls: ['./document-upload.scss'],
})
export class DocumentUploadComponent implements OnInit {

  isLoading = signal(true);
  editMode = signal(false);
  preliminaryDataCompleted = signal(false);
  flatStepIndex = signal(0);
  isUploading = signal(false);
  isFinishing = signal(false);
  uploadError = signal<string | null>(null);
  private currentProcessId = signal<number | null>(null);

  preliminaryForm: FormGroup;
  fireCompanies = ['Primera', 'Segunda', 'Tercera', 'Quinta', 'Sexta', 'Séptima', 'Octava', 'Décima'];
  sections = signal<UploadSection[]>([]);

  private fb = inject(FormBuilder);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private location = inject(Location);
  private uploadService = inject(FileUploadService);
  private historialService = inject(HistorialService);

  allSteps = computed(() => this.sections().flatMap(s => s.steps));
  
  currentSectionIndex = computed(() => {
    const currentFlatIndex = this.flatStepIndex();
    if (currentFlatIndex < 0) return -1;
    
    let stepCount = 0;
    for (let i = 0; i < this.sections().length; i++) {
      stepCount += this.sections()[i].steps.length;
      if (currentFlatIndex < stepCount) return i;
    }
    return this.sections().length - 1;
  });
  
  isCurrentSectionCompleted = computed(() => {
      const sectionIdx = this.currentSectionIndex();
      if (sectionIdx === -1 || sectionIdx >= this.sections().length) return false;
      const currentSection = this.sections()[sectionIdx];
      return currentSection.steps
          .filter(step => !step.optional)
          .every(step => step.isCompleted);
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

  private getSectionKeyForStepTitle(stepTitle: string): string {
    const title = stepTitle.toLowerCase();
    if (title.startsWith('diab')) return 'diab_id';
    if (title.startsWith('reporte flash')) return 'reporte_flash_id';
    if (title.startsWith('informe del obac')) return 'obac_id';
    if (title.startsWith('declaracion de testigos')) return 'declaracion_testigo_id';
    if (title.startsWith('incidente sin lesiones')) return 'incidente_sin_lesiones_id';
    if (title.startsWith('certificado de carabineros')) return 'certificado_carabinero_id';
    if (title.startsWith('dau o variantes')) return 'dau_id';
    if (title.startsWith('orden de atención médica')) return 'certificado_medico_atencion_especial_id';
    if (title.startsWith('informe médico')) return 'informe_medico_id';
    if (title.startsWith('certificado superintendente')) return 'certificado_acreditacion_voluntario_id';
    if (title.startsWith('incidente sin lesiones copia del libro de guardia')) return 'copia_libro_guardia_id';
    if (title.startsWith('copia libro de llamadas')) return 'copia_libro_llamada_id';
    if (title.startsWith('copia aviso de citación')) return 'aviso_citacion_id';
    if (title.startsWith('copia lista de asistencia')) return 'copia_lista_asistencia_id';
    if (title.startsWith('informe ejecutivo')) return 'informe_ejecutivo_id';
    if (title.startsWith('factura establecimiento')) return 'factura_prestaciones_id';
    if (title.startsWith('boletas de honorarios')) return 'boleta_honorario_id';
    if (title.startsWith('boleta de medicamentos')) return 'boleta_medicamentos_id';
    if (title.startsWith('certificado del director')) return 'certificado_examen_id';
    if (title.startsWith('boleta o factura de gastos de traslado')) return 'boleta_factura_traslado_id';
    if (title.startsWith('certificado del médico tratante que determine la incapacidad')) return 'certificado_medico_incapacidad_id';
    if (title.startsWith('certificado médico tratante que justifique la necesidad de traslado')) return 'certificado_medico_traslado_id';
    if (title.startsWith('boleta de gastos de hospedaje')) return 'boleta_gasto_acompanante_id';
    if (title.startsWith('boleta de gastos de alimentación')) return 'boleta_alimentacion_acompanante_id';
    if (title.startsWith('otros')) return 'otros_gastos_id';
    return '';
  }

  private setupEditMode(id: string): void {
      this.isLoading.set(true);
      this.editMode.set(true);
      this.currentProcessId.set(Number(id));
      
      this.uploadService.getProcessById(id).subscribe({
          next: (record) => {
              this.preliminaryForm.patchValue({ bomberoNombre: record.bombero_name, compania: record.company });
              this.preliminaryForm.disable();

              const sections = this.getInitialSections();
              
              sections.forEach(section => {
                  section.steps.forEach(step => {
                      const backendKey = this.getSectionKeyForStepTitle(step.title);
                          const documentId = record[backendKey];

                      if (documentId) {
                            const matchingDoc = record.documents.find((doc: any) => doc.id === documentId);
                            if (matchingDoc) {
                                step.files = [{ ...matchingDoc, fromServer: true, name: matchingDoc.file_name }];
                                step.isCompleted = true;
                            }
                      } 
                        else if (step.optional) {
                            step.isCompleted = true;
                        }
                  });
              });
              const allSteps = sections.flatMap(s => s.steps);
              const firstIncompleteIndex = allSteps.findIndex(step => !step.isCompleted);
              this.flatStepIndex.set(firstIncompleteIndex === -1 ? allSteps.length - 1 : firstIncompleteIndex);

              this.sections.set(sections);
              this.preliminaryDataCompleted.set(true);
              this.isLoading.set(false);
          },
          error: (err) => { this.uploadError.set('Error al cargar el proceso.'); this.isLoading.set(false); }
      });
  }
  
  private setupCreateMode(): void {
    this.editMode.set(false);
    this.preliminaryDataCompleted.set(false);
    this.preliminaryForm.reset();
    this.preliminaryForm.enable();
    this.currentProcessId.set(null);
    this.sections.set(this.getInitialSections());
    this.flatStepIndex.set(0);
    this.isLoading.set(false);
  }

  submitPreliminaryData() {
    if (this.preliminaryForm.invalid) return;
    this.isUploading.set(true);
    this.uploadService.createProcess(this.preliminaryForm.value)
      .pipe(finalize(() => this.isUploading.set(false)))
      .subscribe({
        next: (response) => {
          this.historialService.fetchHistory().subscribe();
          this.currentProcessId.set(response.id);
          this.preliminaryDataCompleted.set(true);
          this.preliminaryForm.disable();
          this.editMode.set(false);
          this.flatStepIndex.set(0);

          this.location.replaceState(`/document-upload/${response.id}`);
        },
        error: (err) => { this.uploadError.set('Error al crear el proceso.'); }
      });
  }
  
  getFlatIndex(sectionIndex: number, stepIndex: number): number {
    let flatIndex = 0;
    for (let i = 0; i < sectionIndex; i++) {
      flatIndex += this.sections()[i].steps.length;
    }
    return flatIndex + stepIndex;
  }

  public nextStep(): void {
    const currentIndex = this.flatStepIndex();
    const currentStep = this.allSteps()[currentIndex];
    
    if ((currentStep.optional && currentStep.files.length === 0) || currentStep.files.length > 0) {
        currentStep.isCompleted = true;
        this.sections.update(s => [...s]);
    }

    this.advanceToNextStep();
  }

  public advanceToNextStep(): void {
    const currentIndex = this.flatStepIndex();
    const totalSteps = this.allSteps().length;
    
    if (currentIndex < totalSteps - 1) {
      this.flatStepIndex.set(currentIndex + 1);
    } else {
      console.log('Todos los pasos han sido mostrados.');
      const lastStep = this.allSteps()[currentIndex];
      if (lastStep && (lastStep.optional || lastStep.files.length > 0)) {
        lastStep.isCompleted = true;
      }
      this.sections.update(s => [...s]);
    }
  }

  handleFiles(fileList: FileList | null, step: UploadStep): void {
    const processId = this.currentProcessId();
    if (!processId) return;
    if (!fileList || fileList.length === 0) return;

    const file = fileList[0];
    step.files.push(file); 
    step.isUploading = true;
    
    this.uploadService.uploadFile(processId, step.title, file)
      .pipe(finalize(() => { step.isUploading = false; }))
      .subscribe({
        next: (response) => {
          if (response && response.document) {
            step.isCompleted = true;
            const newFiles = step.files.filter(f => f.name !== file.name);
            newFiles.push({ ...response.document, fromServer: true, name: response.document.file_name });
            step.files = newFiles;
            this.sections.update(s => [...s]);
            this.advanceToNextStep();
          } else {
            this.uploadError.set(`Respuesta inesperada del servidor para: ${file.name}`);
            step.files = step.files.filter(f => f.name !== file.name);
            this.sections.update(s => [...s]);
          }
        },
        error: (err) => {
          this.uploadError.set(`Error al subir el archivo: ${file.name}`);
          step.files = step.files.filter(f => f.name !== file.name);
          this.sections.update(s => [...s]);
        }
      });
  }

  removeFile(step: UploadStep, fileToRemove: File | ServerFile): void {
    const wasCompleted = step.isCompleted; 
    
    if (this.isServerFile(fileToRemove)) {
      this.uploadService.deleteFile(fileToRemove.id).subscribe({
        next: () => {
          step.files = step.files.filter(f => f !== fileToRemove);
          
          let needsReactivation = false;

          if (step.files.length === 0 && !step.optional) {
              step.isCompleted = false;
              needsReactivation = true;
          }
          this.sections.update(s => [...s]);

          if (needsReactivation) {
            const allSteps = this.allSteps();
            const firstIncompleteIndex = allSteps.findIndex(s => !s.isCompleted);
            if (firstIncompleteIndex !== -1) {
                this.flatStepIndex.set(firstIncompleteIndex);
            }
          }

        },
        error: (err) => { this.uploadError.set(`Error al eliminar el archivo "${fileToRemove.name}" del servidor.`); }
      });
    } else {
      step.files = step.files.filter(f => f !== fileToRemove);
      let needsReactivation = false;
      
      if (step.files.length === 0 && !step.optional) {
        step.isCompleted = false;
        needsReactivation = true;
      }
      this.sections.update(s => [...s]);
      if (needsReactivation) {
        const allSteps = this.allSteps();
        const firstIncompleteIndex = allSteps.findIndex(s => !s.isCompleted);
        
        if (firstIncompleteIndex !== -1) {
            this.flatStepIndex.set(firstIncompleteIndex);
        }
      }
    }
  }

  finishProcess(): void {
    this.isFinishing.set(true);
    this.router.navigate(['/historial']);
  }

  isServerFile(file: any): file is ServerFile {
      return file.fromServer === true;
  }
  
  saveChanges(): void {
      this.router.navigate(['/historial']);
  }
  
  togglePaidStatus(step: UploadStep): void {
      step.isPaid = !step.isPaid;
      this.sections.update(sections => [...sections]);
  }

  onDragOver(event: DragEvent) { event.preventDefault(); (event.currentTarget as HTMLElement).classList.add('drag-over'); }
  onDragLeave(event: DragEvent) { event.preventDefault(); (event.currentTarget as HTMLElement).classList.remove('drag-over'); }
  onDrop(event: DragEvent, step: UploadStep) {
    event.preventDefault();
    (event.currentTarget as HTMLElement).classList.remove('drag-over');
    if (event.dataTransfer?.files) {
      this.handleFiles(event.dataTransfer.files, step);
    }
  }


onViewFile(file: ServerFile): void {
    this.uploadService.viewFile(file.id).subscribe(blob => {
      const fileURL = URL.createObjectURL(blob);
      window.open(fileURL, '_blank'); // Abre el archivo en una nueva pestaña
    });
  }

  onDownloadFile(file: ServerFile): void {
    this.uploadService.downloadFile(file.id).subscribe(blob => {
      const a = document.createElement('a');
      const objectUrl = URL.createObjectURL(blob);
      a.href = objectUrl;
      a.download = file.name; // Usa el nombre original del archivo
      a.click();
      URL.revokeObjectURL(objectUrl); // Libera memoria
    });
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
            { title: 'Incidente sin lesiones Copia del Libro de Guardia (no legalizado y solo el registro del accidente)', optional: true, files: [], isCompleted: false },
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
            { title: 'Boletas de Honorarios Profesionales, no incluidas en factura, visadas por medico jefe del servicio.', optional: true, files: [], isCompleted: false, isPaid: false },
            { title: 'Boleta de Medicamentos y prescripción correspondiente.', optional: false, files: [], isCompleted: false, isPaid: false },
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

