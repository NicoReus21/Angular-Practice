import { ChangeDetectionStrategy, Component, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSelectModule } from '@angular/material/select';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { forkJoin } from 'rxjs';
import {
  AuthDirectoryService,
  ApiGroup,
  ApiPermission,
  ApiUser
} from '../../services/auth-directory.service';

@Component({
  selector: 'app-group-access',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatButtonModule,
    MatCardModule,
    MatFormFieldModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatSelectModule,
    MatSnackBarModule
  ],
  templateUrl: './group-access.html',
  styleUrl: './group-access.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class GroupAccessComponent {
  private route = inject(ActivatedRoute);
  private authDirectory = inject(AuthDirectoryService);
  private snackBar = inject(MatSnackBar);

  readonly groupId = Number(this.route.snapshot.paramMap.get('id'));

  readonly group = signal<ApiGroup | null>(null);
  readonly groupUsers = signal<ApiUser[]>([]);
  readonly groupPermissions = signal<ApiPermission[]>([]);
  readonly permissions = signal<ApiPermission[]>([]);
  readonly isLoading = signal(true);
  readonly errorMessage = signal<string | null>(null);
  readonly isSavingPermission = signal(false);
  private readonly busyUserIds = signal<Set<number>>(new Set());
  private readonly busyPermissionIds = signal<Set<number>>(new Set());
  readonly selectedPermissionId = signal<number | null>(null);

  readonly usersCount = computed(() => this.groupUsers().length);
  readonly permissionCount = computed(() => this.groupPermissions().length);
  readonly availablePermissions = computed(() => {
    const assigned = new Set(this.groupPermissions().map((perm) => perm.id));
    return this.permissions()
      .filter((perm) => !assigned.has(perm.id))
      .sort((a, b) => this.permissionLabel(a).localeCompare(this.permissionLabel(b)));
  });

  constructor() {
    if (Number.isNaN(this.groupId)) {
      this.isLoading.set(false);
      this.errorMessage.set('Grupo no valido.');
      return;
    }

    this.loadData();
  }

  isUserBusy(userId: number): boolean {
    return this.busyUserIds().has(userId);
  }

  removeUser(userId: number): void {
    if (this.isUserBusy(userId)) {
      return;
    }

    this.setBusy(userId, true);
    this.authDirectory.removeUserFromGroup(userId, this.groupId).subscribe({
      next: () => this.loadUsers(),
      error: (err) => {
        console.error(err);
        this.setBusy(userId, false);
      }
    });
  }

  addPermission(): void {
    const permissionId = this.selectedPermissionId();
    if (!permissionId || this.isSavingPermission()) {
      return;
    }

    this.isSavingPermission.set(true);
    this.authDirectory.assignPermissionToGroup(this.groupId, permissionId).subscribe({
      next: () => {
        this.selectedPermissionId.set(null);
        this.loadGroupPermissions();
        this.snackBar.open('Permiso asignado al grupo.', 'Cerrar', { duration: 3000 });
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudo asignar el permiso.', 'Cerrar', { duration: 3500 });
      },
      complete: () => this.isSavingPermission.set(false)
    });
  }

  removePermission(permissionId: number): void {
    if (this.isPermissionBusy(permissionId)) {
      return;
    }

    this.setPermissionBusy(permissionId, true);
    this.authDirectory.revokePermissionFromGroup(this.groupId, permissionId).subscribe({
      next: () => {
        this.loadGroupPermissions();
        this.snackBar.open('Permiso removido del grupo.', 'Cerrar', { duration: 3000 });
      },
      error: (err) => {
        console.error(err);
        this.snackBar.open('No se pudo quitar el permiso.', 'Cerrar', { duration: 3500 });
        this.setPermissionBusy(permissionId, false);
      }
    });
  }

  isPermissionBusy(permissionId: number): boolean {
    return this.busyPermissionIds().has(permissionId);
  }

  permissionLabel(permission: ApiPermission): string {
    return `${permission.module} / ${permission.section} / ${permission.action}`;
  }

  private loadData(): void {
    this.isLoading.set(true);
    this.errorMessage.set(null);

    forkJoin({
      group: this.authDirectory.getGroup(this.groupId),
      users: this.authDirectory.getGroupUsers(this.groupId),
      permissions: this.authDirectory.getPermissions(),
      groupPermissions: this.authDirectory.getGroupPermissions(this.groupId)
    }).subscribe({
      next: ({ group, users, permissions, groupPermissions }) => {
        this.group.set(group);
        this.groupUsers.set(users || []);
        this.permissions.set(permissions || []);
        this.groupPermissions.set(groupPermissions || []);
      },
      error: (err) => {
        console.error(err);
        this.errorMessage.set('No se pudo cargar la informacion del grupo.');
      },
      complete: () => this.isLoading.set(false)
    });
  }

  private loadUsers(): void {
    this.authDirectory.getGroupUsers(this.groupId).subscribe({
      next: (users) => {
        this.groupUsers.set(users || []);
      },
      error: (err) => console.error(err),
      complete: () => this.setBusy(undefined, false)
    });
  }

  private loadGroupPermissions(): void {
    this.authDirectory.getGroupPermissions(this.groupId).subscribe({
      next: (permissions) => {
        this.groupPermissions.set(permissions || []);
      },
      error: (err) => console.error(err),
      complete: () => this.setPermissionBusy(undefined, false)
    });
  }

  private setBusy(id: number | undefined, value: boolean): void {
    const updated = new Set(this.busyUserIds());
    if (id !== undefined) {
      if (value) {
        updated.add(id);
      } else {
        updated.delete(id);
      }
      this.busyUserIds.set(updated);
      return;
    }

    if (!value) {
      this.busyUserIds.set(new Set());
    }
  }

  private setPermissionBusy(id: number | undefined, value: boolean): void {
    const updated = new Set(this.busyPermissionIds());
    if (id !== undefined) {
      if (value) {
        updated.add(id);
      } else {
        updated.delete(id);
      }
      this.busyPermissionIds.set(updated);
      return;
    }

    if (!value) {
      this.busyPermissionIds.set(new Set());
    }
  }
}
