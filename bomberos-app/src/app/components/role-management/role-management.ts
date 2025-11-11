import { Component, OnInit, inject, signal } from '@angular/core';
import { FormBuilder, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';
import { RoleService } from '../../services/role-service';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { MatDividerModule } from '@angular/material/divider';
import { CommonModule } from '@angular/common';
import Swal from 'sweetalert2';
import { MatDialog } from '@angular/material/dialog';
import { EditRoleDialogComponent } from '../../components/edit-role-dialog/edit-role-dialog';

@Component({
  selector: 'app-role-management',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatTableModule,
    MatDividerModule
  ],
  templateUrl: './role-management.html',
  styleUrl: './role-management.scss'
})
export class RoleManagementComponent implements OnInit {
  private fb = inject(FormBuilder);
  private roleService = inject(RoleService);
  private snackBar = inject(MatSnackBar); 
  roles = signal<any[]>([]);
  isSubmitting = signal(false);
  private dialog = inject(MatDialog);

  displayedColumns: string[] = ['id', 'name', 'actions'];

  roleForm = this.fb.group({
    name: ['', Validators.required]
  });

  ngOnInit(): void {
    this.loadRoles();
  }

  loadRoles(): void {
    this.roleService.getRoles().subscribe({
      next: (data) => this.roles.set(data || []),
      error: (err) => {
        console.error(err);
        this.snackBar.open('Error al cargar los roles', 'Cerrar', { duration: 3000 });
      }
    });
  }

  onSubmit(): void {
    if (this.roleForm.invalid || this.isSubmitting()) {
      return;
    }

    this.isSubmitting.set(true);
    const roleName = this.roleForm.value.name!;

    this.roleService.createRole(roleName).subscribe({
      next: (newRole) => {
        this.snackBar.open(`Rol "${newRole.name}" creado con éxito`, 'Cerrar', {
          duration: 3000,
          panelClass: 'success-snackbar'
        });
        this.roleForm.reset();
        this.roles.update(currentRoles => [...currentRoles, newRole]);
      },
      error: (err) => {
        console.error(err);
        let errorMsg = 'Error al crear el rol.';
        if (err.status === 422) {
          errorMsg = 'El nombre del rol ya existe.';
        }
        this.snackBar.open(errorMsg, 'Cerrar', {
          duration: 3000,
          panelClass: 'error-snackbar'
        });
      },
      complete: () => this.isSubmitting.set(false)
    });
  }

  onEdit(role: any): void {
    const dialogRef = this.dialog.open(EditRoleDialogComponent, {
      width: '550px',
      data: { role: role },
      autoFocus: "dialog"
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === true) {
        this.snackBar.open(`Permisos para "${role.name}" actualizados.`, 'Cerrar', {
          duration: 3000,
          panelClass: 'success-snackbar'
        });
      }
    });
  }

  onDelete(id: number, name: string): void {
    
    Swal.fire({
      title: `¿Estás seguro?`,
      text: `Se eliminará el rol "${name}". ¡Esta acción no se puede deshacer!`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
      if (result.isConfirmed) {
        
        this.roleService.deleteRole(id).subscribe({
          next: () => {
            Swal.fire(
              '¡Eliminado!',
              `El rol "${name}" ha sido eliminado.`,
              'success'
            );
            this.roles.update(currentRoles => currentRoles.filter(r => r.id !== id)); 
          },
          error: (err) => {
            console.error(err);
            Swal.fire(
              'Error',
              'Ocurrió un error al eliminar el rol.',
              'error'
            );
          }
        });
      }
    });
  }
}