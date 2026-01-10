import { ChangeDetectionStrategy, Component, OnDestroy, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth-service';
import { PermissionStoreService } from '../../services/permission-store.service';

@Component({
  selector: 'app-auth-transition',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './auth-transition.html',
  styleUrl: './auth-transition.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class AuthTransitionComponent implements OnInit, OnDestroy {
  private authService = inject(AuthService);
  private permissionStore = inject(PermissionStoreService);
  private router = inject(Router);
  private timeoutId?: number;

  ngOnInit(): void {
    if (!this.authService.getToken()) {
      this.router.navigate(['/login']);
      return;
    }

    this.permissionStore.load().subscribe({
      next: (permissions) => {
        const permissionKeys = permissions.map(
          (permission) => `${permission.module}:${permission.section}:${permission.action}`
        );
        const hasModules = permissionKeys.includes('Sistema:Modules:read');
        const target = hasModules ? '/modules' : '/landing';
        this.timeoutId = window.setTimeout(() => this.router.navigate([target]), 900);
      },
      error: () => {
        this.timeoutId = window.setTimeout(() => this.router.navigate(['/landing']), 900);
      }
    });
  }

  ngOnDestroy(): void {
    if (this.timeoutId) {
      window.clearTimeout(this.timeoutId);
    }
  }
}
