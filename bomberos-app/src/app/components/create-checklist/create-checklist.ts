import { Component, ChangeDetectionStrategy, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormGroup,
  Validators,
  ReactiveFormsModule,
  FormsModule,
} from '@angular/forms';
import { MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { provideNativeDateAdapter } from '@angular/material/core';
import { MatListModule } from '@angular/material/list';
import { MatDividerModule } from '@angular/material/divider';

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

  checklistForm: FormGroup;
  tasks = signal<ChecklistTask[]>([]);
  newTaskName = signal<string>('');

  constructor() {
    this.checklistForm = this.fb.group({
      personaCargo: ['', Validators.required],
      fechaRealizacion: [new Date(), Validators.required],
    });
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
