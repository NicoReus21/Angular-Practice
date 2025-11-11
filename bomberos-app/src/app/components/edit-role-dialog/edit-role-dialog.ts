import { Component, Inject, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; 
import { MAT_DIALOG_DATA, MatDialogRef, MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatDividerModule } from '@angular/material/divider';
import { RoleService } from '../../services/role-service'; 
import { forkJoin } from 'rxjs'; 
import Swal from 'sweetalert2'; 

@Component({
  selector: 'app-edit-role-dialog',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule, 
    MatDialogModule,
    MatButtonModule,
    MatCheckboxModule,
    MatProgressSpinnerModule, 
    MatDividerModule
  ],
  templateUrl: './edit-role-dialog.html',
  styleUrl: './edit-role-dialog.scss'
})
export class EditRoleDialogComponent implements OnInit {
  private roleService = inject(RoleService);
  public dialogRef = inject(MatDialogRef<EditRoleDialogComponent>);
  
  // Inyectamos los datos del rol que abrimos (ej. { id: 5, name: 'Capitán' })
  constructor(@Inject(MAT_DIALOG_DATA) public data: { role: any }) {}

  isLoading = signal(true);
  allPermissions = signal<any[]>([]);
  
  // Usamos un "mapa" para rastrear el estado de cada checkbox
  // Ejemplo: { 1: true, 2: false, 3: true }
  permissionSelection: { [key: number]: boolean } = {};

  ngOnInit(): void {
    // Hacemos 2 llamadas a la API al mismo tiempo
    forkJoin({
      allPermissions: this.roleService.getAllPermissions(),
      rolePermissions: this.roleService.getRolePermissions(this.data.role.id)
    }).subscribe({
      next: ({ allPermissions, rolePermissions }) => {
        
        this.allPermissions.set(allPermissions);
        
        // Creamos el mapa de selección
        const rolePermIds = new Set(rolePermissions.map(p => p.id));
        this.permissionSelection = {};
        for (const perm of allPermissions) {
          this.permissionSelection[perm.id] = rolePermIds.has(perm.id);
        }
        
        this.isLoading.set(false);
      },
      error: (err) => {
        console.error(err);
        this.isLoading.set(false);
        Swal.fire('Error', 'No se pudieron cargar los permisos', 'error');
        this.dialogRef.close();
      }
    });
  }

  onSave(): void {
    this.isLoading.set(true);
    const selectedIds = Object.keys(this.permissionSelection)
      .filter(id => this.permissionSelection[+id])
      .map(id => +id);

    this.roleService.syncRolePermissions(this.data.role.id, selectedIds).subscribe({
      next: () => {
        this.isLoading.set(false);
        this.dialogRef.close(true); 
      },
      error: (err) => {
        console.error(err);
        this.isLoading.set(false);
        Swal.fire('Error', 'No se pudieron guardar los permisos', 'error');
      }
    });
  }

  onClose(): void {
    this.dialogRef.close(); 
  }
}