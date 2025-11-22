import { Component, ChangeDetectionStrategy, inject, signal } from '@angular/core'; // signal AÑADIDO
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-create-firetruck',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatIconModule
  ],
  templateUrl: './create-firetruck.html',
  styleUrl: './create-firetruck.scss',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class CreateFiretruckComponent {

  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateFiretruckComponent>);
  unitForm: FormGroup;

  private selectedImageFile: File | null = null;
  public selectedImageName = signal<string | null>(null);

  constructor() {
    this.unitForm = this.fb.group({
      name: ['', Validators.required],
      plate: ['', Validators.required],
      model: [''],
      company: ['', Validators.required],
      status: ['En Servicio', Validators.required],
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onImageSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedImageFile = input.files[0];
      this.selectedImageName.set(this.selectedImageFile.name);
    } else {
      this.selectedImageFile = null;
      this.selectedImageName.set(null);
    }
  }

  onSave(): void {
    if (this.unitForm.valid) {
      this.dialogRef.close({
        formData: this.unitForm.value,
        imageFile: this.selectedImageFile 
      });
    } else {
      this.unitForm.markAllAsTouched();
    }
  }
}