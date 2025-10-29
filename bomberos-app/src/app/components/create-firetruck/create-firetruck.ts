import { Component, ChangeDetectionStrategy, inject } from '@angular/core';
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

  constructor() {
    this.unitForm = this.fb.group({
      name: ['', Validators.required],
      plate: ['', Validators.required],
      model: [''],
      company: ['', Validators.required],
      status: ['En Servicio', Validators.required],
      imageUrl: ['https_placehold.co/600x400/3498db/white?text=Nueva+Unidad&font=roboto']
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSave(): void {
    if (this.unitForm.valid) {
      this.dialogRef.close(this.unitForm.value);
    } else {
      this.unitForm.markAllAsTouched();
    }
  }
}

