import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

// Importaciones de Angular Material para este componente
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';

// Interface para definir la estructura de los datos del historial
export interface HistorialElement {
  id: string;
  nombre: string;
  fecha: string;
  estado: string;
}

// Datos de ejemplo
const ELEMENT_DATA: HistorialElement[] = [
  {id: 'SBA-01', nombre: 'Juan Pérez', fecha: '20/08/2025', estado: 'Completado'},
  {id: 'SBA-02', nombre: 'María González', fecha: '25/08/2025', estado: 'En Revisión'},
];

@Component({
  selector: 'app-historial',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatToolbarModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule
  ],
  templateUrl: './historial.html',
  styleUrls: ['./historial.scss']
})
export class HistorialComponent {
  displayedColumns: string[] = ['id', 'nombre', 'fecha', 'estado', 'acciones'];
  dataSource = ELEMENT_DATA;
}