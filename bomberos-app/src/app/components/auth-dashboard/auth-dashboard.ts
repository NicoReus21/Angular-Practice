import { ChangeDetectionStrategy, Component, OnInit, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatTableModule } from '@angular/material/table';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { RouterLink } from '@angular/router';
import { ReactiveFormsModule, FormBuilder, Validators, FormControl } from '@angular/forms';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { RoleManagementComponent } from '../role-management/role-management';
import { AuthDirectoryService, ApiGroup, ApiUser } from '../../services/auth-directory.service';
import { RoleService } from '../../services/role-service';
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
  name: string;
  description?: string | null;
}

interface DashboardSection {
  id: 'usuarios' | 'roles' | 'grupos' | 'permisos';
  label: string;
  icon: string;
  description: string;
}

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

  readonly userError = signal<string | null>(null);
  readonly groupError = signal<string | null>(null);
  readonly permissionError = signal<string | null>(null);
  readonly isManaging = signal(false);

  readonly isSavingUserAccess = signal(false);
  readonly isSavingGroup = signal(false);
  readonly isSavingGroupPermissions = signal(false);
  readonly isSavingGroupUsers = signal(false);

  readonly selectedUser = signal<UserRecord | null>(null);
  readonly selectedGroup = signal<GroupRecord | null>(null);

  userAccessForm = this.fb.group({
    userId: this.fb.control<number | null>(null, Validators.required),
    groupIds: this.fb.control<number[]>([], Validators.required),
    roleIds: this.fb.control<number[]>([], Validators.required)
  });

  groupForm = this.fb.group({
    name: ['', [Validators.required, Validators.minLength(3)]],
    description: ['']
  });

  groupPermissionForm = this.fb.group({
    groupId: this.fb.control<number | null>(null, Validators.required),
    permissionIds: this.fb.control<number[]>([], Validators.required)
  });

  groupUsersForm = this.fb.group({
    groupId: this.fb.control<number | null>(null, Validators.required),
    userIds: this.fb.control<number[]>([], Validators.required)
  });

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
    if (!this.isManaging()) {
      this.isManaging.set(true);
    }
    this.selectedSection.set(this.sections.find((s) => s.id === 'usuarios')!);
    this.selectedUser.set(user);
    this.userAccessForm.patchValue({
      userId: user.id,
      groupIds: user.groupIds || [],
      roleIds: user.roleIds || []
    });
  }

  manageGroup(group: GroupRecord): void {
    if (!this.isManaging()) {
      this.isManaging.set(true);
    }
    this.selectedSection.set(this.sections.find((s) => s.id === 'grupos')!);
    this.selectedGroup.set(group);
    this.groupPermissionForm.patchValue({
      groupId: group.id,
      permissionIds: group.permissionIds || []
    });
    this.groupUsersForm.patchValue({
      groupId: group.id,
      userIds: group.userIds || []
    });
  }

  submitUserAccess(): void {
    if (this.userAccessForm.invalid || this.isSavingUserAccess()) {
      this.userAccessForm.markAllAsTouched();
      return;
    }

    const { userId, groupIds, roleIds } = this.userAccessForm.value;
    this.isSavingUserAccess.set(true);

    forkJoin([
      this.authDirectory.assignGroupsToUser(userId!, groupIds || []),
      this.authDirectory.assignRolesToUser(userId!, roleIds || [])
    ]).subscribe({
      next: () => {
        this.snackBar.open('Accesos del usuario actualizados.', 'Cerrar', { duration: 3000 });
        this.loadUsers();
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudieron guardar los cambios del usuario.', 'Cerrar', {
          duration: 4000
        });
      },
      complete: () => this.isSavingUserAccess.set(false)
    });
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

  submitGroupUsers(): void {
    if (this.groupUsersForm.invalid || this.isSavingGroupUsers()) {
      this.groupUsersForm.markAllAsTouched();
      return;
    }

    const { groupId, userIds } = this.groupUsersForm.value;
    this.isSavingGroupUsers.set(true);

    const requests = (userIds || []).map((userId) =>
      this.authDirectory.assignUserToGroup(userId, groupId!)
    );

    if (!requests.length) {
      this.isSavingGroupUsers.set(false);
      return;
    }

    forkJoin(requests).subscribe({
      next: () => {
        this.snackBar.open('Usuarios asignados al grupo.', 'Cerrar', { duration: 3000 });
        this.loadGroups();
        this.loadUsers();
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudieron vincular los usuarios.', 'Cerrar', { duration: 4000 });
      },
      complete: () => this.isSavingGroupUsers.set(false)
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
    return {
      id: group.id,
      name: group.name,
      description: group.description || 'Sin descripcion',
      users: (group as any).users || undefined,
      usersCount:
        typeof group.users_count === 'number'
          ? group.users_count
          : ((group as any).users ? (group as any).users.length : undefined),
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
      name: permission.name || permission.guard_name || `Permiso ${permission.id}`,
      description: permission.description || permission.guard_name || null
    };
  }

  permissionLabel(permission: PermissionRecord): string {
    const desc = permission.description ? ` - ${permission.description}` : '';
    return `${permission.id}. ${permission.name}${desc}`;
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
      this.groupUsersForm.patchValue({
        userIds: target.userIds || []
      });
    }
  }
}
