import { ChangeDetectionStrategy, Component, OnInit, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatTableModule } from '@angular/material/table';
import { RoleManagementComponent } from '../role-management/role-management';
import { AuthDirectoryService, ApiGroup, ApiUser } from '../../services/auth-directory.service';
import { RoleService } from '../../services/role-service';

interface UserRecord {
  id: number;
  name: string;
  email: string;
  company: string;
  groups: string[];
  roles: string[];
  status: 'Activo' | 'Suspendido' | 'Pendiente';
}

interface GroupRecord {
  id: number;
  name: string;
  description?: string;
  usersCount?: number;
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
    RoleManagementComponent
  ],
  templateUrl: './auth-dashboard.html',
  styleUrl: './auth-dashboard.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class AuthDashboardComponent implements OnInit {
  private authDirectory = inject(AuthDirectoryService);
  private roleService = inject(RoleService);

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

  readonly displayedColumns = ['name', 'groups', 'roles', 'status'];

  readonly users = signal<UserRecord[]>([]);
  readonly groups = signal<GroupRecord[]>([]);
  readonly permissions = signal<PermissionRecord[]>([]);

  readonly isLoadingUsers = signal(false);
  readonly isLoadingGroups = signal(false);
  readonly isLoadingPermissions = signal(false);

  readonly userError = signal<string | null>(null);
  readonly groupError = signal<string | null>(null);
  readonly permissionError = signal<string | null>(null);

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

  private mapUser(user: ApiUser): UserRecord {
    return {
      id: user.id,
      name: user.name || 'Usuario sin nombre',
      email: user.email || 'Sin email',
      company: user.company || 'Sin compania',
      groups: (user.groups || []).map((group) => group.name || 'Grupo'),
      roles: (user.roles || []).map((role) => role.name || 'Rol'),
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
      usersCount: group.users_count,
      createdAt: group.created_at
    };
  }

  private mapPermission(permission: any): PermissionRecord {
    return {
      id: permission.id,
      name: permission.name,
      description: permission.description || permission.guard_name || null
    };
  }
}
