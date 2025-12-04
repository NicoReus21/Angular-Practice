import { ChangeDetectionStrategy, Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { AuthService } from '../../services/auth-service';

interface ModuleCard {
  title: string;
  subtitle: string;
  description: string;
  icon: string;
  route?: string;
  ctaLabel?: string;
  available: boolean;
  tags?: string[];
}

@Component({
  selector: 'app-modules-overview',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatCardModule,
    MatButtonModule,
    MatIconModule
  ],
  templateUrl: './modules-overview.html',
  styleUrl: './modules-overview.scss',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class ModulesOverviewComponent {
  private authService = inject(AuthService);

  readonly modules: ModuleCard[] = [
    {
      title: 'Bombero accidentado',
      subtitle: 'Documentación y seguimiento',
      description:
        'Inicia nuevos procesos, sube la documentación requerida y sigue el estado de cada caso.',
      icon: 'health_and_safety',
      route: '/historial',
      ctaLabel: 'Gestionar casos',
      available: true,
      tags: ['Casos activos', 'Expedientes']
    },
    {
      title: 'Material mayor',
      subtitle: 'Control del parque automotriz',
      description:
        'Consulta y actualiza el historial del material mayor para asegurar la disponibilidad de equipos.',
      icon: 'fire_truck',
      route: '/machine-historial',
      ctaLabel: 'Revisar unidades',
      available: true,
      tags: ['Inventario', 'Operativo']
    },
    {
      title: 'Autenticación',
      subtitle: 'Usuarios y roles',
      description:
        'Administra las credenciales, permisos y accesos necesarios para tu compañía.',
      icon: 'admin_panel_settings',
      route: '/rols',
      ctaLabel: 'Gestionar accesos',
      available: true,
      tags: ['Seguridad', 'Permisos']
    },
    {
      title: 'Próximamente más',
      subtitle: 'Nuevos desarrollos en curso',
      description:
        'Estamos preparando más módulos para ampliar el alcance del sistema SIGBA.',
      icon: 'construction',
      available: false,
      tags: ['En diseño']
    }
  ];

  logout(): void {
    this.authService.logout();
  }
}

