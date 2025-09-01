import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

// Importaciones de Angular Material para este componente
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatStepperModule } from '@angular/material/stepper';

@Component({
  selector: 'app-document-upload',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatToolbarModule,
    MatButtonModule,
    MatIconModule,
    MatStepperModule
  ],
  templateUrl: './document-upload.html',
  styleUrls: ['./document-upload.scss']
})
export class DocumentUploadComponent {
  // Datos de los pasos del proceso, extraídos del script original
  steps = [
    { title: "Cheque médico mutual", optional: false },
    { title: "Informe médico", optional: false },
    { title: "Resumen de atención urgencias", optional: false },
    { title: "DIAB", optional: false },
    { title: "Documento Capitán", optional: false },
    { title: "OBAC", optional: false },
    { title: "Testigo", optional: true }
  ];

  // Función para manejar la selección de archivos (puedes expandir esta lógica)
  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      const file = input.files[0];
      console.log('Archivo seleccionado:', file.name);
      // Aquí puedes agregar la lógica para subir el archivo a un servidor
    }
  }
}