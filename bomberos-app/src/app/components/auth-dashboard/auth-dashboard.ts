import { ChangeDetectionStrategy, Component, computed, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatTableModule } from '@angular/material/table';
import { RoleManagementComponent } from '../role-management/role-management';

interface UserRecord {
  name: string;
  email: string;
  company: string;
  groups: string[];
  roles: string[];
  status: 'Activo' | 'Suspendido' | 'Pendiente';
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
export class AuthDashboardComponent {
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
      description: 'Administra los roles, permisos y su asignación.'
    },
    {
      id: 'grupos',
      label: 'Grupos',
      icon: 'group_work',
      description: 'Organiza las compañías o brigadas en grupos operativos.'
    },
    {
      id: 'permisos',
      label: 'Permisos',
      icon: 'verified_user',
      description: 'Define alcances específicos y combina permisos por rol.'
    }
  ];

  readonly selectedSection = signal<DashboardSection>(this.sections[0]);
  readonly activeSectionId = computed(() => this.selectedSection().id);

  readonly displayedColumns = ['name', 'groups', 'roles', 'status'];

  readonly users = signal<UserRecord[]>([
    {
      name: 'Carla Pérez',
      email: 'cperez@bomberos.cl',
      company: '2da Compañía',
      groups: ['Central', 'Logística'],
      roles: ['Administradora', 'Revisora'],
      status: 'Activo'
    },
    {
      name: 'Miguel Torres',
      email: 'mtorres@bomberos.cl',
      company: '4ta Compañía',
      groups: ['Rescate Urbano'],
      roles: ['Supervisor'],
      status: 'Pendiente'
    },
    {
      name: 'Andrea González',
      email: 'agonzalez@bomberos.cl',
      company: '1ra Compañía',
      groups: ['Comando', 'RRHH'],
      roles: ['Operadora'],
      status: 'Activo'
    },
    {
      name: 'Felipe Mena',
      email: 'fmena@bomberos.cl',
      company: '3ra Compañía',
      groups: ['Apoyo Médico'],
      roles: ['Colaborador'],
      status: 'Suspendido'
    }
  ]);

  readonly activeUsersCount = computed(
    () => this.users().filter((user) => user.status === 'Activo').length
  );

  readonly pendingUsersCount = computed(
    () => this.users().filter((user) => user.status === 'Pendiente').length
  );

  readonly suspendedUsersCount = computed(
    () => this.users().filter((user) => user.status === 'Suspendido').length
  );

  selectSection(section: DashboardSection): void {
    this.selectedSection.set(section);
  }

  isActive(section: DashboardSection): boolean {
    return this.activeSectionId() === section.id;
  }
}
