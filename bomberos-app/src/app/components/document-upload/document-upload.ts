import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

export interface UploadStep {
  title: string;
  optional: boolean;
  files: File[];
  isCompleted: boolean;
}

@Component({
  selector: 'app-document-upload',
  standalone: true,
  imports: [
    CommonModule,
    MatToolbarModule,
    MatButtonModule,
    MatIconModule,
  ],
  templateUrl: './document-upload.html',
  styleUrls: ['./document-upload.scss'],
})
export class DocumentUploadComponent {

  currentStep = signal(-1);

steps = signal<UploadStep[]>([
  { title: 'Cheque médico mutual', optional: false, files: [], isCompleted: false },
  { title: 'Informe médico', optional: false, files: [], isCompleted: false },
  { title: 'Resumen de atención urgencias', optional: false, files: [], isCompleted: false },
  { title: 'DIAB', optional: false, files: [], isCompleted: false },
  { title: 'Documento Capitán', optional: false, files: [], isCompleted: false },
  { title: 'OBAC', optional: false, files: [], isCompleted: false },
  { title: 'Testigo', optional: true, files: [], isCompleted: false },
]);

  constructor(private router: Router) {}

startProcess(): void {
  this.currentStep.set(0);
}

nextStep(): void {
    const current = this.currentStep();
    if (current < this.steps().length) {
      // Marca el paso actual como completado
    this.steps.update(steps => {
      if (steps[current]) {
          steps[current].isCompleted = true;
      }
      return steps;
    });

    if (current < this.steps().length - 1) {
      this.currentStep.set(current + 1);
    }else {
      this.finishProcess();
    }
  }
}


handleFiles(fileList: FileList, stepIndex: number): void {
  this.steps.update(steps => {
    steps[stepIndex].files = Array.from(fileList);
    return steps;
  });
}

finishProcess(): void {
  console.log('¡Proceso completado!', this.steps());
  this.currentStep.set(this.steps().length); 
  setTimeout(() => {
    this.router.navigate(['/historial']);
  }, 2000);
}

onDragOver(event: DragEvent) {
  event.preventDefault();
  (event.currentTarget as HTMLElement).classList.add('drag-over');
}

onDragLeave(event: DragEvent) {
  event.preventDefault();
  (event.currentTarget as HTMLElement).classList.remove('drag-over');
}

onDrop(event: DragEvent, stepIndex: number) {
  event.preventDefault();
  (event.currentTarget as HTMLElement).classList.remove('drag-over');
  if (event.dataTransfer?.files) {
      this.handleFiles(event.dataTransfer.files, stepIndex);
    }
  }
}