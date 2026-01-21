import { Component, ChangeDetectionStrategy, signal, inject } from '@angular/core';
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
import { MatListModule } from '@angular/material/list';
import { MatDividerModule } from '@angular/material/divider';
// Asegúrate de que la ruta de importación sea correcta según tu estructura
import { ChecklistGroup } from '../../services/machine-historial'; 

export interface ChecklistTask {
  id: string;
  task: string;
  completed: boolean;
}

@Component({
  selector: 'app-create-checklist',
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
    MatListModule,
    MatDividerModule,
  ],
  templateUrl: './create-checklist.html',
  styleUrls: ['./create-checklist.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
  providers: [provideNativeDateAdapter()],
})
export class CreateChecklistComponent {
  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateChecklistComponent>);
  
  // INYECCIÓN DE DATOS: Aquí recibimos lo que enviaste desde machine-historial.ts
  public data = inject<{ editMode: boolean; checklist: ChecklistGroup }>(MAT_DIALOG_DATA);

  checklistForm: FormGroup;
  tasks = signal<ChecklistTask[]>([]);
  newTaskName = signal<string>('');

  // Variables para textos dinámicos
  dialogTitle = 'Registrar incidente';
  submitButtonLabel = 'Guardar incidente';

  constructor() {
    this.checklistForm = this.fb.group({
      personaCargo: ['', Validators.required],
      fechaRealizacion: [new Date(), Validators.required],
    });

    // --- LÓGICA DE EDICIÓN ---
    if (this.data && this.data.checklist) {
      this.dialogTitle = 'Editar incidente';
      this.submitButtonLabel = 'Actualizar incidente';

      // 1. Parsear la fecha (La API suele enviar string "YYYY-MM-DD")
      let fecha = new Date();
      const rawDate = this.data.checklist.fecha_realizacion;
      
      if (typeof rawDate === 'string') {
        // Intenta convertir string a fecha. 
        // Si viene como "DD-MM-YYYY" (formato chileno que usas en la vista), hay que invertirlo para el objeto Date
        if (rawDate.includes('-')) {
           const parts = rawDate.split('-');
           // Si es YYYY-MM-DD (estándar API)
           if (parts[0].length === 4) {
             fecha = new Date(rawDate + 'T00:00:00'); // Agregamos hora para evitar problemas de zona horaria
           } 
           // Si es DD-MM-YYYY (formato visual)
           else {
             fecha = new Date(+parts[2], +parts[1] - 1, +parts[0]);
           }
        }
      }

      // 2. Rellenar el formulario
      this.checklistForm.patchValue({
        personaCargo: this.data.checklist.persona_cargo,
        fechaRealizacion: fecha,
      });

      // 3. Rellenar las tareas
      if (this.data.checklist.items && this.data.checklist.items.length > 0) {
        const loadedTasks: ChecklistTask[] = this.data.checklist.items.map(item => ({
          id: String(item.id), // Convertimos a string para que coincida con tu interfaz local
          task: item.task_description,
          completed: item.completed
        }));
        this.tasks.set(loadedTasks);
      }
    }
  }

  onAddTask(): void {
    const taskName = this.newTaskName().trim();
    if (taskName) {
      const newTask: ChecklistTask = {
        id: `task-${Math.random().toString(36).substring(2, 9)}`,
        task: taskName,
        completed: false,
      };
      this.tasks.update((currentTasks) => [...currentTasks, newTask]);
      this.newTaskName.set('');
    }
  }

  onRemoveTask(taskId: string): void {
    this.tasks.update((currentTasks) => currentTasks.filter((task) => task.id !== taskId));
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    if (this.checklistForm.valid && this.tasks().length > 0) {
      this.dialogRef.close({
        formData: this.checklistForm.value,
        tasks: this.tasks(),
      });
    } else {
      this.checklistForm.markAllAsTouched();
    }
  }
}
