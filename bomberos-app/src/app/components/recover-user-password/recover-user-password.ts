import { Component, inject, signal } from '@angular/core';
import {
  FormBuilder,
  Validators,
  ReactiveFormsModule,
} from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar } from '@angular/material/snack-bar';
import { HttpErrorResponse } from '@angular/common/http';
import { AuthService } from '../../services/auth-service'; 

@Component({
  selector: 'app-recover-password',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    MatButtonModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule,
  ],
  templateUrl: './recover-user-password.html',
  styleUrls: ['./recover-user-password.scss'],
})
export class RecoverPasswordComponent {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private router = inject(Router);
  private snackBar = inject(MatSnackBar);

  isSubmitting = signal(false);

  recoverForm = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
  });

  goToLogin(): void {
    this.router.navigate(['/login']);
  }

  onSubmit(): void {
    if (this.recoverForm.invalid || this.isSubmitting()) {
      return;
    }

    this.isSubmitting.set(true);
    const { email } = this.recoverForm.value;

    // Llama a tu servicio de autenticación (ajusta el nombre del método si es necesario)
    // Si no tienes el método aún en el servicio, puedes simularlo aquí temporalmente
    this.authService.recoverPassword(email!).subscribe({
      next: () => {
        this.snackBar.open(
          'Se ha enviado un enlace de recuperación a su correo.',
          'Cerrar',
          {
            duration: 5000,
            panelClass: 'success-snackbar',
          }
        );
        // Opcional: Redirigir al login tras unos segundos
        setTimeout(() => this.goToLogin(), 3000);
      },
      error: (err: HttpErrorResponse) => {
        console.error(err);
        let errorMessage = 'No se pudo enviar el correo. Intente nuevamente.';
        
        if (err.status === 404) {
          errorMessage = 'El correo ingresado no se encuentra registrado.';
        }

        this.snackBar.open(errorMessage, 'Cerrar', {
          duration: 5000,
          panelClass: 'error-snackbar',
        });
        this.isSubmitting.set(false);
      },
      complete: () => {
        this.isSubmitting.set(false);
      },
    });
  }
}