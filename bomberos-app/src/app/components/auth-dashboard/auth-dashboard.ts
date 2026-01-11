import { ChangeDetectionStrategy, Component, OnInit, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatTableModule } from '@angular/material/table';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { RouterLink, Router } from '@angular/router';
import {
  ReactiveFormsModule,
  FormBuilder,
  Validators,
  AbstractControl,
  ValidationErrors,
  ValidatorFn
} from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { RoleManagementComponent } from '../role-management/role-management';
import { AuthDirectoryService, ApiGroup, ApiUser } from '../../services/auth-directory.service';
import { RoleService } from '../../services/role-service';
import { AuthService } from '../../services/auth-service';
import { forkJoin } from 'rxjs';

interface UserRecord {
  id: number;
  name: string;
  email: string;
  company: string;
  groups: string[];
  roles: string[];
  groupIds: number[];
  roleIds: number[];
  status: 'Activo' | 'Suspendido' | 'Pendiente';
}

interface GroupRecord {
  id: number;
  name: string;
  description?: string;
  usersCount?: number;
  userIds?: number[];
  permissionIds?: number[];
  users?: any[];
  createdAt?: string;
}

interface PermissionRecord {
  id: number;
  module: string;
  section: string;
  action: string;
  description?: string | null;
}

interface DashboardSection {
  id: 'usuarios' | 'roles' | 'grupos' | 'permisos' | 'password';
  label: string;
  icon: string;
  description: string;
}

const passwordMatchValidator: ValidatorFn = (control: AbstractControl): ValidationErrors | null => {
  const newPassword = control.get('new_password')?.value;
  const confirmPassword = control.get('new_password_confirmation')?.value;

  if (!newPassword || !confirmPassword) {
    return null;
  }

  return newPassword === confirmPassword ? null : { passwordMismatch: true };
};

@Component({
  selector: 'app-auth-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    MatIconModule,
    MatButtonModule,
    MatCardModule,
    MatTableModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    ReactiveFormsModule,
    MatSnackBarModule,
    RouterLink,
    RoleManagementComponent
  ],
  templateUrl: './auth-dashboard.html',
  styleUrl: './auth-dashboard.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class AuthDashboardComponent implements OnInit {
  private authDirectory = inject(AuthDirectoryService);
  private roleService = inject(RoleService);
  private authService = inject(AuthService);
  private router = inject(Router);
  private fb = inject(FormBuilder);
  private snackBar = inject(MatSnackBar);

  readonly sections: DashboardSection[] = [
    {
      id: 'usuarios',
      label: 'Usuarios',
      icon: 'group',
      description: 'Consulta los usuarios y revisa su pertenencia a grupos y roles.'
    },
    {
      id: 'roles',
      label: 'Roles',
      icon: 'shield_person',
      description: 'Administra los roles, permisos y su asignacion.'
    },
    {
      id: 'grupos',
      label: 'Grupos',
      icon: 'group_work',
      description: 'Organiza las companias o brigadas en grupos operativos.'
    },
    {
      id: 'permisos',
      label: 'Permisos',
      icon: 'verified_user',
      description: 'Define alcances especificos y combina permisos por rol.'
    },
    {
      id: 'password',
      label: 'Contrasena',
      icon: 'lock',
      description: 'Actualiza tu contrasena para mantener la cuenta segura.'
    }
  ];

  readonly selectedSection = signal<DashboardSection>(this.sections[0]);
  readonly activeSectionId = computed(() => this.selectedSection().id);

  readonly displayedColumns = ['name', 'groups', 'roles', 'status', 'actions'];

  readonly users = signal<UserRecord[]>([]);
  readonly groups = signal<GroupRecord[]>([]);
  readonly permissions = signal<PermissionRecord[]>([]);
  readonly allRoles = signal<any[]>([]);

  readonly isLoadingUsers = signal(false);
  readonly isLoadingGroups = signal(false);
  readonly isLoadingPermissions = signal(false);
  readonly isLoadingGroupUsers = signal(false);

  readonly userError = signal<string | null>(null);
  readonly groupError = signal<string | null>(null);
  readonly permissionError = signal<string | null>(null);
  readonly groupUsersError = signal<string | null>(null);
  readonly isManaging = signal(false);

  readonly isSavingGroup = signal(false);
  readonly isSavingGroupPermissions = signal(false);
  readonly isSavingPassword = signal(false);

  readonly selectedGroup = signal<GroupRecord | null>(null);
  readonly groupUsers = signal<ApiUser[]>([]);

  groupForm = this.fb.group({
    name: ['', [Validators.required, Validators.minLength(3)]],
    description: ['']
  });

  groupPermissionForm = this.fb.group({
    groupId: this.fb.control<number | null>(null, Validators.required),
    permissionIds: this.fb.control<number[]>([], Validators.required)
  });

  groupUsersForm = this.fb.group({
    groupId: this.fb.control<number | null>(null, Validators.required)
  });

  passwordForm = this.fb.group(
    {
      current_password: ['', [Validators.required]],
      new_password: ['', [Validators.required, Validators.minLength(6)]],
      new_password_confirmation: ['', [Validators.required]]
    },
    { validators: passwordMatchValidator }
  );

  readonly activeUsersCount = computed(
    () => this.users().filter((user) => user.status === 'Activo').length
  );

  readonly pendingUsersCount = computed(
    () => this.users().filter((user) => user.status === 'Pendiente').length
  );

  readonly suspendedUsersCount = computed(
    () => this.users().filter((user) => user.status === 'Suspendido').length
  );

  ngOnInit(): void {
    this.loadUsers();
    this.loadGroups();
    this.loadPermissions();
    this.loadRoles();
  }

  selectSection(section: DashboardSection): void {
    this.selectedSection.set(section);
  }

  isActive(section: DashboardSection): boolean {
    return this.activeSectionId() === section.id;
  }

  loadUsers(): void {
    this.isLoadingUsers.set(true);
    this.userError.set(null);

    this.authDirectory.getUsers().subscribe({
      next: (data: ApiUser[]) => {
        const mappedUsers = (data || []).map((user) => this.mapUser(user));
        this.users.set(mappedUsers);
      },
      error: (err) => {
        console.error(err);
        this.userError.set('No se pudieron cargar los usuarios.');
        this.users.set([]);
      },
      complete: () => this.isLoadingUsers.set(false)
    });
  }

  loadGroups(): void {
    this.isLoadingGroups.set(true);
    this.groupError.set(null);

    this.authDirectory.getGroups().subscribe({
      next: (data: ApiGroup[]) => {
        const mappedGroups = (data || []).map((group) => this.mapGroup(group));
        this.groups.set(mappedGroups);
      },
      error: (err) => {
        console.error(err);
        this.groupError.set('No se pudieron cargar los grupos.');
        this.groups.set([]);
      },
      complete: () => this.isLoadingGroups.set(false)
    });
  }

  loadPermissions(): void {
    this.isLoadingPermissions.set(true);
    this.permissionError.set(null);

    this.roleService.getAllPermissions().subscribe({
      next: (data: any[]) => {
        const mappedPermissions = (data || []).map((permission) => this.mapPermission(permission));
        this.permissions.set(mappedPermissions);
      },
      error: (err) => {
        console.error(err);
        this.permissionError.set('No se pudieron cargar los permisos.');
        this.permissions.set([]);
      },
      complete: () => this.isLoadingPermissions.set(false)
    });
  }

  loadRoles(): void {
    this.roleService.getRoles().subscribe({
      next: (data: any[]) => this.allRoles.set(data || []),
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudieron cargar los roles.', 'Cerrar', { duration: 3500 });
      }
    });
  }

  toggleManagementPanel(): void {
    this.isManaging.update((value) => !value);
  }

  manageUser(user: UserRecord): void {
    this.router.navigate(['/rols/user', user.id]);
  }

  manageGroup(group: GroupRecord): void {
    this.router.navigate(['/rols/group', group.id]);
  }

  submitGroup(): void {
    if (this.groupForm.invalid || this.isSavingGroup()) {
      this.groupForm.markAllAsTouched();
      return;
    }

    this.isSavingGroup.set(true);
    const { name, description } = this.groupForm.value;

    this.authDirectory.createGroup({ name: name!, description: description || null }).subscribe({
      next: (newGroup) => {
        this.groups.update((current) => [...current, this.mapGroup(newGroup)]);
        this.snackBar.open('Grupo creado con exito.', 'Cerrar', { duration: 3000 });
        this.groupForm.reset();
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudo crear el grupo.', 'Cerrar', { duration: 4000 });
      },
      complete: () => this.isSavingGroup.set(false)
    });
  }

  submitGroupPermissions(): void {
    if (this.groupPermissionForm.invalid || this.isSavingGroupPermissions()) {
      this.groupPermissionForm.markAllAsTouched();
      return;
    }

    const { groupId, permissionIds } = this.groupPermissionForm.value;
    this.isSavingGroupPermissions.set(true);

    const requests = (permissionIds || []).map((permissionId) =>
      this.authDirectory.assignPermissionToGroup(groupId!, permissionId)
    );

    if (!requests.length) {
      this.isSavingGroupPermissions.set(false);
      return;
    }

    forkJoin(requests).subscribe({
      next: () => {
        this.snackBar.open('Permisos del grupo actualizados.', 'Cerrar', { duration: 3000 });
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudieron asignar los permisos.', 'Cerrar', { duration: 4000 });
      },
      complete: () => this.isSavingGroupPermissions.set(false)
    });
  }

  submitPasswordChange(): void {
    if (this.passwordForm.invalid || this.isSavingPassword()) {
      this.passwordForm.markAllAsTouched();
      return;
    }

    const { current_password, new_password, new_password_confirmation } = this.passwordForm.value;
    this.isSavingPassword.set(true);

    this.authService
      .changePassword(current_password!, new_password!, new_password_confirmation!)
      .subscribe({
        next: () => {
          this.snackBar.open('Contrasena actualizada correctamente.', 'Cerrar', {
            duration: 3000
          });
          this.passwordForm.reset();
        },
        error: (err) => {
          console.error(err);
          const message =
            err?.status === 401
              ? 'La contrasena actual es incorrecta.'
              : 'No se pudo actualizar la contrasena.';
          this.snackBar.open(message, 'Cerrar', { duration: 4000 });
        },
        complete: () => this.isSavingPassword.set(false)
      });
  }

  private mapUser(user: ApiUser): UserRecord {
    return {
      id: user.id,
      name: user.name || 'Usuario sin nombre',
      email: user.email || 'Sin email',
      company: user.company || 'Sin compania',
      groups: (user.groups || []).map((group) => group.name || 'Grupo'),
      roles: (user.roles || []).map((role) => role.name || 'Rol'),
      groupIds: (user.groups || []).map((group) => group.id),
      roleIds: (user.roles || []).map((role) => role.id),
      status: this.normalizeStatus(user.status)
    };
  }

  private normalizeStatus(rawStatus?: string | null): UserRecord['status'] {
    const status = (rawStatus || '').toLowerCase();

    if (status.includes('suspend')) {
      return 'Suspendido';
    }

    if (status.includes('pend')) {
      return 'Pendiente';
    }

    return 'Activo';
  }

  private mapGroup(group: ApiGroup): GroupRecord {
    const usersCount =
      group.users_count !== undefined && group.users_count !== null
        ? Number(group.users_count)
        : undefined;

    return {
      id: group.id,
      name: group.name,
      description: group.description || 'Sin descripcion',
      users: (group as any).users || undefined,
      usersCount: usersCount ?? ((group as any).users ? (group as any).users.length : undefined),
      userIds: (group as any).users ? (group as any).users.map((u: any) => u.id) : undefined,
      permissionIds: (group as any).permissions
        ? (group as any).permissions.map((p: any) => p.id)
        : undefined,
      createdAt: group.created_at
    };
  }

  private mapPermission(permission: any): PermissionRecord {
    return {
      id: permission.id,
      module: permission.module || 'Sin modulo',
      section: permission.section || 'Sin seccion',
      action: permission.action || 'Sin accion',
      description: permission.description || null
    };
  }

  permissionLabel(permission: PermissionRecord): string {
    const desc = permission.description ? ` - ${permission.description}` : '';
    return `${permission.module}:${permission.section}:${permission.action}${desc}`;
  }

  onPermissionGroupChange(groupId: number): void {
    const target = this.groups().find((g) => g.id === groupId);
    if (target) {
      this.selectedGroup.set(target);
      this.groupPermissionForm.patchValue({
        permissionIds: target.permissionIds || []
      });
    }
  }

  onUsersGroupChange(groupId: number): void {
    const target = this.groups().find((g) => g.id === groupId);
    if (target) {
      this.selectedGroup.set(target);
      this.loadGroupUsers(groupId);
    }
  }

  loadGroupUsers(groupId: number): void {
    this.isLoadingGroupUsers.set(true);
    this.groupUsersError.set(null);

    this.authDirectory.getGroupUsers(groupId).subscribe({
      next: (users) => {
        this.groupUsers.set(users || []);
      },
      error: (err) => {
        console.error(err);
        this.groupUsersError.set('No se pudieron cargar los usuarios del grupo.');
        this.groupUsers.set([]);
      },
      complete: () => this.isLoadingGroupUsers.set(false)
    });
  }

  removeUserFromGroup(userId: number, groupId: number): void {
    if (!groupId) {
      return;
    }
    this.authDirectory.removeUserFromGroup(userId, groupId).subscribe({
      next: () => {
        this.loadGroupUsers(groupId);
        this.loadGroups();
        this.loadUsers();
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudo quitar el usuario del grupo.', 'Cerrar', {
          duration: 4000
        });
      }
    });
  }
}
