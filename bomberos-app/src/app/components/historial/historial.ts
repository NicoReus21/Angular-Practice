import { Component, inject, OnInit } from '@angular/core'; 
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
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
export class HistorialComponent implements OnInit {
  private historialService = inject(HistorialService);
  private router = inject(Router); 

  displayedColumns: string[] = ['id', 'nombre', 'compania', 'fecha', 'estado', 'acciones'];

  dataSource = this.historialService.historyRecords;

  ngOnInit(): void {
    this.historialService.fetchHistory().subscribe({
      next: (data) => {
        this.checkPendingDocumentation(data);
      },
      error: (err) => {
        console.error('Error al cargar el historial:', err);
        Swal.fire({
          title: 'Error',
          text: 'No se pudo cargar el historial.',
          icon: 'error'
        });
      }
    });
  }

  async checkPendingDocumentation(records: HistorialElement[]) {
    const pendingRecords = records.filter(r => 
      r.estado !== 'Finalizado' && r.estado !== 'Completado'
    );
    
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: true,
      confirmButtonText: 'Completar',
      confirmButtonColor: '#3085d6',
      timer: 4000, 
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
    for (const element of pendingRecords) {
      
      const result = await Toast.fire({
        icon: 'warning',
        title: 'Documentación Pendiente',
        text: `Falta documentación por completar de ${element.bombero_name}`
      });

      if (result.isConfirmed) {
        this.router.navigate(['/document-upload', element.id]);
        break; 
      }
    }
  }

  confirmDelete(element: HistorialElement): void {
    Swal.fire({
      title: '¿Estás seguro?',
      text: `Se eliminará el registro de "${element.bombero_name}".`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.historialService.deleteRecord(element.id).subscribe({
          next: () => {
            Swal.fire('¡Eliminado!', 'El registro ha sido eliminado.', 'success');
            this.historialService.fetchHistory().subscribe();
          },
          error: (err) => {
            Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
          }
        });
      }
    });
  }
}