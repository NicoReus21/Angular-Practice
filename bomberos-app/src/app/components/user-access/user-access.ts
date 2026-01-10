import { ChangeDetectionStrategy, Component, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSelectModule } from '@angular/material/select';
import { ReactiveFormsModule, FormControl, Validators } from '@angular/forms';
import { forkJoin } from 'rxjs';
import {
  AuthDirectoryService,
  ApiGroup,
  ApiRole,
  ApiUser
} from '../../services/auth-directory.service';
import { RoleService } from '../../services/role-service';

interface PermissionRecord {
  id: number;
  name: string;
  description?: string | null;
}

interface RoleWithPermissions extends ApiRole {
  permissions?: PermissionRecord[];
}

interface TableRow {
  id: number;
  name: string;
  assigned: boolean;
}

@Component({
  selector: 'app-user-access',
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
    ReactiveFormsModule
  ],
  templateUrl: './user-access.html',
  styleUrl: './user-access.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class UserAccessComponent {
  private route = inject(ActivatedRoute);
  private authDirectory = inject(AuthDirectoryService);
  private roleService = inject(RoleService);

  readonly userId = Number(this.route.snapshot.paramMap.get('id'));

  readonly user = signal<ApiUser | null>(null);
  readonly allGroups = signal<ApiGroup[]>([]);
  readonly userGroups = signal<ApiGroup[]>([]);
  readonly allRoles = signal<ApiRole[]>([]);
  readonly userRoles = signal<RoleWithPermissions[]>([]);
  readonly permissions = signal<PermissionRecord[]>([]);

  readonly isLoading = signal(true);
  readonly errorMessage = signal<string | null>(null);
  readonly isAssigningGroups = signal(false);
  readonly isAssigningRoles = signal(false);

  private readonly busyGroupIds = signal<Set<number>>(new Set());
  private readonly busyRoleIds = signal<Set<number>>(new Set());

  readonly groupSelect = new FormControl<number[]>([], {
    nonNullable: true,
    validators: [Validators.required]
  });

  readonly roleSelect = new FormControl<number[]>([], {
    nonNullable: true,
    validators: [Validators.required]
  });

  readonly groupRows = computed<TableRow[]>(() =>
    this.allGroups().map((group) => ({
      id: group.id,
      name: group.name,
      assigned: this.userGroups().some((userGroup) => userGroup.id === group.id)
    }))
  );

  readonly availableGroups = computed(() =>
    this.groupRows().filter((group) => !group.assigned)
  );

  readonly assignedGroups = computed(() =>
    this.userGroups().map((group) => ({ id: group.id, name: group.name }))
  );

  readonly roleRows = computed<TableRow[]>(() =>
    this.allRoles().map((role) => ({
      id: role.id,
      name: role.name,
      assigned: this.userRoles().some((userRole) => userRole.id === role.id)
    }))
  );

  readonly availableRoles = computed(() =>
    this.roleRows().filter((role) => !role.assigned)
  );

  readonly assignedRoles = computed(() =>
    this.userRoles().map((role) => ({ id: role.id, name: role.name }))
  );

  readonly sortedPermissions = computed(() =>
    [...this.permissions()].sort((a, b) => a.name.localeCompare(b.name))
  );

  constructor() {
    if (Number.isNaN(this.userId)) {
      this.isLoading.set(false);
      this.errorMessage.set('Usuario no valido.');
      return;
    }

    this.loadData();
  }

  isGroupBusy(groupId: number): boolean {
    return this.busyGroupIds().has(groupId);
  }

  isRoleBusy(roleId: number): boolean {
    return this.busyRoleIds().has(roleId);
  }

  assignSelectedGroups(): void {
    const groupIds = this.groupSelect.value;
    if (!groupIds.length || this.isAssigningGroups()) {
      return;
    }

    this.isAssigningGroups.set(true);

    forkJoin(groupIds.map((groupId) => this.authDirectory.assignUserToGroup(this.userId, groupId)))
      .subscribe({
        next: () => {
          this.groupSelect.reset([], { emitEvent: false });
          this.refreshUserGroups();
        },
        error: (err) => {
          console.error(err);
          this.isAssigningGroups.set(false);
        }
      });
  }

  removeAssignedGroup(groupId: number): void {
    if (this.isGroupBusy(groupId)) {
      return;
    }

    this.setBusy(this.busyGroupIds, groupId, true);
    this.authDirectory.removeUserFromGroup(this.userId, groupId).subscribe({
      next: () => this.refreshUserGroups(),
      error: (err) => {
        console.error(err);
        this.setBusy(this.busyGroupIds, groupId, false);
      }
    });
  }

  assignSelectedRoles(): void {
    const roleIds = this.roleSelect.value;
    if (!roleIds.length || this.isAssigningRoles()) {
      return;
    }

    this.isAssigningRoles.set(true);

    forkJoin(roleIds.map((roleId) => this.authDirectory.assignRoleToUser(this.userId, roleId)))
      .subscribe({
        next: () => {
          this.roleSelect.reset([], { emitEvent: false });
          this.refreshUserRoles();
        },
        error: (err) => {
          console.error(err);
          this.isAssigningRoles.set(false);
        }
      });
  }

  removeAssignedRole(roleId: number): void {
    if (this.isRoleBusy(roleId)) {
      return;
    }

    this.setBusy(this.busyRoleIds, roleId, true);
    this.authDirectory.removeRoleFromUser(this.userId, roleId).subscribe({
      next: () => this.refreshUserRoles(),
      error: (err) => {
        console.error(err);
        this.setBusy(this.busyRoleIds, roleId, false);
      }
    });
  }

  private loadData(): void {
    this.isLoading.set(true);
    this.errorMessage.set(null);

    forkJoin({
      user: this.authDirectory.getUser(this.userId),
      groups: this.authDirectory.getGroups(),
      userGroups: this.authDirectory.getUserGroups(this.userId),
      roles: this.roleService.getRoles(),
      userRoles: this.authDirectory.getUserRoles(this.userId)
    }).subscribe({
      next: ({ user, groups, userGroups, roles, userRoles }) => {
        this.user.set(user);
        this.allGroups.set(groups || []);
        this.userGroups.set(userGroups || []);
        this.allRoles.set(roles || []);
        this.userRoles.set((userRoles || []) as RoleWithPermissions[]);
        this.permissions.set(this.buildPermissionList(userRoles as RoleWithPermissions[]));
      },
      error: (err) => {
        console.error(err);
        this.errorMessage.set('No se pudo cargar la informacion del usuario.');
      },
      complete: () => this.isLoading.set(false)
    });
  }

  private refreshUserGroups(): void {
    this.authDirectory.getUserGroups(this.userId).subscribe({
      next: (groups) => {
        this.userGroups.set(groups || []);
      },
      error: (err) => console.error(err),
      complete: () => {
        this.setBusy(this.busyGroupIds, undefined, false);
        this.isAssigningGroups.set(false);
      }
    });
  }

  private refreshUserRoles(): void {
    this.authDirectory.getUserRoles(this.userId).subscribe({
      next: (roles) => {
        const typedRoles = (roles || []) as RoleWithPermissions[];
        this.userRoles.set(typedRoles);
        this.permissions.set(this.buildPermissionList(typedRoles));
      },
      error: (err) => console.error(err),
      complete: () => {
        this.setBusy(this.busyRoleIds, undefined, false);
        this.isAssigningRoles.set(false);
      }
    });
  }

  private buildPermissionList(roles: RoleWithPermissions[]): PermissionRecord[] {
    const permissionMap = new Map<number, PermissionRecord>();

    roles.forEach((role) => {
      (role.permissions || []).forEach((permission) => {
        if (!permissionMap.has(permission.id)) {
          permissionMap.set(permission.id, permission);
        }
      });
    });

    return Array.from(permissionMap.values());
  }

  private setBusy(target: typeof this.busyGroupIds, id: number | undefined, value: boolean): void {
    const updated = new Set(target());
    if (id !== undefined) {
      if (value) {
        updated.add(id);
      } else {
        updated.delete(id);
      }
      target.set(updated);
      return;
    }

    if (!value) {
      target.set(new Set());
    }
  }
}
