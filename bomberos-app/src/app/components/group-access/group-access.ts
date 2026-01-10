import { ChangeDetectionStrategy, Component, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { forkJoin } from 'rxjs';
import { AuthDirectoryService, ApiGroup, ApiUser } from '../../services/auth-directory.service';

@Component({
  selector: 'app-group-access',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatButtonModule,
    MatCardModule,
    MatIconModule,
    MatProgressSpinnerModule
  ],
  templateUrl: './group-access.html',
  styleUrl: './group-access.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class GroupAccessComponent {
  private route = inject(ActivatedRoute);
  private authDirectory = inject(AuthDirectoryService);

  readonly groupId = Number(this.route.snapshot.paramMap.get('id'));

  readonly group = signal<ApiGroup | null>(null);
  readonly groupUsers = signal<ApiUser[]>([]);
  readonly isLoading = signal(true);
  readonly errorMessage = signal<string | null>(null);
  private readonly busyUserIds = signal<Set<number>>(new Set());

  readonly usersCount = computed(() => this.groupUsers().length);

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

  private loadData(): void {
    this.isLoading.set(true);
    this.errorMessage.set(null);

    forkJoin({
      group: this.authDirectory.getGroup(this.groupId),
      users: this.authDirectory.getGroupUsers(this.groupId)
    }).subscribe({
      next: ({ group, users }) => {
        this.group.set(group);
        this.groupUsers.set(users || []);
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
}
