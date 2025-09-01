// src/app/app.config.ts

import { ApplicationConfig, importProvidersFrom } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { provideAnimations } from '@angular/platform-browser/animations';

// Importa los módulos de Angular Material que necesitas
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatStepperModule } from '@angular/material/stepper';

export const appConfig: ApplicationConfig = {
  providers: [
    provideRouter(routes),
    // 1. Añade el proveedor de animaciones
    provideAnimations(), 
    // 2. Importa los proveedores de los módulos de Material
    importProvidersFrom(
      MatCardModule,
      MatFormFieldModule,
      MatInputModule,
      MatButtonModule,
      MatToolbarModule,
      MatTableModule,
      MatIconModule,
      MatStepperModule
      // Añade aquí cualquier otro módulo de Material que vayas a usar
    )
  ]
};