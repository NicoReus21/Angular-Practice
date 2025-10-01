import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { HistorialService, HistorialElement } from '../../services/historial';
import Swal from 'sweetalert2';
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
  
  displayedColumns: string[] = ['id', 'nombre', 'compania', 'fecha', 'estado', 'acciones'];
  
  dataSource = this.historialService.historyRecords;

  confirmDelete(element: HistorialElement): void {
    Swal.fire({
      title: '¿Estás seguro?',
      text: `Se eliminará el registro de "${element.bombero_nombre}". Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: 'red',
      cancelButtonColor: 'blue',
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.historialService.deleteRecord(element.id).subscribe({
          next: () => {
            Swal.fire(
              '¡Eliminado!',
              'El registro ha sido eliminado.',
              'success'
            );
            this.historialService.fetchHistory().subscribe();
          },
          error: (err) => {
            Swal.fire(
              'Error',
              'No se pudo eliminar el registro. Intente de nuevo.',
              'error'
            );
            console.error('Error al eliminar:', err);
          }
        });
      }
    });
  }


}
