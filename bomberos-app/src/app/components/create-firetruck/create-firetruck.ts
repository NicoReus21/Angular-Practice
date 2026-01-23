import { Component, ChangeDetectionStrategy, inject, signal, OnInit, Inject, Optional } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatDialogModule, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
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
  styleUrls: ['./create-firetruck.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class CreateFiretruckComponent implements OnInit {

  private fb = inject(FormBuilder);
  public dialogRef = inject(MatDialogRef<CreateFiretruckComponent>);
  
  unitForm: FormGroup;
  
  private selectedImageFile: File | null = null;
  public selectedImageName = signal<string | null>(null);
  public currentImageUrl = signal<string | null>(null); 
  public isEditMode = signal<boolean>(false);

  constructor(@Optional() @Inject(MAT_DIALOG_DATA) public data: any) {
    this.unitForm = this.fb.group({
      name: ['', Validators.required],
      plate: ['', Validators.required],
      model: [''],
      // CORRECCIÓN: Usar snake_case para que coincida con la base de datos Laravel
      manufacturing_year: [''], 
      chassis_number: [''],     
      company: ['', Validators.required],
      status: ['En Servicio', Validators.required],
    });
  }

  ngOnInit(): void {
    if (this.data && this.data.unit) {
      this.isEditMode.set(true);
      const unit = this.data.unit;
      
      // Mapeamos los datos existentes al formulario
      // Intentamos leer tanto snake_case como camelCase por seguridad
      this.unitForm.patchValue({
        name: unit.name,
        plate: unit.plate,
        model: unit.model,
        manufacturing_year: unit.manufacturing_year,
        chassis_number: unit.chassis_number,
        company: unit.company,
        status: unit.status
      });
      if (unit.imageUrl) {
        this.currentImageUrl.set(unit.imageUrl);
      }
    }
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

  onRemoveNewImage(): void {
    this.selectedImageFile = null;
    this.selectedImageName.set(null);
  }

  onSave(): void {
    if (this.unitForm.valid) {
      // Enviamos this.unitForm.value, que ahora tiene las claves en snake_case correctas
      this.dialogRef.close({
        formData: this.unitForm.value,
        imageFile: this.selectedImageFile,
        id: this.isEditMode() ? this.data.unit.id : null 
      });
    } else {
      this.unitForm.markAllAsTouched();
    }
  }
}