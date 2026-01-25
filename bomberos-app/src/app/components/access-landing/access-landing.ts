import { ChangeDetectionStrategy, Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { AuthService } from '../../services/auth-service';

@Component({
  selector: 'app-access-landing',
  standalone: true,
  imports: [CommonModule, MatButtonModule, MatCardModule, MatIconModule],
  templateUrl: './access-landing.html',
  styleUrl: './access-landing.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class AccessLandingComponent {
  private authService = inject(AuthService);

  logout(): void {
    this.authService.logout();
  }
}
