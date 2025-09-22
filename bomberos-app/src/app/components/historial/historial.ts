import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
// Importa el nuevo servicio y su interface
import { HistorialService, HistorialElement } from '../../services/historial';

@Component({
  selector: 'app-historial',
  standalone: true,
  imports: [
    CommonModule, RouterLink, MatTableModule, MatButtonModule, MatIconModule
  ],
  templateUrl: './historial.html',
  styleUrls: ['./historial.scss']
})
export class HistorialComponent {
  private historialService = inject(HistorialService);
  
  // Las columnas ahora incluyen la compañía
  displayedColumns: string[] = ['id', 'nombre', 'compania', 'fecha', 'estado', 'acciones'];
  
  // El dataSource ahora es la señal del servicio
  dataSource = this.historialService.historyRecords;
}
