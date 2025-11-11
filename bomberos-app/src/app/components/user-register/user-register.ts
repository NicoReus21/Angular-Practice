import { Component, inject, signal } from '@angular/core';
import {
  FormBuilder,
  Validators,
  ReactiveFormsModule,
  AbstractControl,
  ValidationErrors,
} from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService } from '../../services/auth-service'
import { HttpErrorResponse } from '@angular/common/http';

export function passwordsMatchValidator(
  control: AbstractControl
): ValidationErrors | null {
  const password = control.get('password');
  const confirmPassword = control.get('confirmPassword');

  if (password?.value !== confirmPassword?.value) {
    return { passwordsMismatch: true };
  }
  return null;
}

@Component({
  selector: 'app-user-register',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
  ],
  templateUrl: './user-register.html',
  styleUrl: './user-register.scss',
})
export class UserRegisterComponent {
  private fb = inject(FormBuilder);
  private authService = inject(AuthService);
  private router = inject(Router);
  private snackBar = inject(MatSnackBar);
  hidePassword = signal(true);
  isSubmitting = signal(false);
  
  registerForm = this.fb.group(
    {
      name: ['', [Validators.required, Validators.minLength(3)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      confirmPassword: ['', [Validators.required]],
    },
    {
      validators: passwordsMatchValidator,
    }
  );


  togglePasswordVisibility(event: MouseEvent): void {
    event.stopPropagation(); 
    this.hidePassword.set(!this.hidePassword());
  }

  // Método para manejar el envío del formulario
  onSubmit(): void {
    if (this.registerForm.invalid || this.isSubmitting()) {
      return;
    }

    this.isSubmitting.set(true);
    
    const { name, email, password } = this.registerForm.value;

    this.authService.register(name!, email!, password!).subscribe({
      next: (response) => {
        this.snackBar.open('Usuario registrado con éxito', 'Cerrar', {
          duration: 3000,
          panelClass: 'success-snackbar',
        });
        this.router.navigate(['/admin/usuarios']); 
      },
      error: (err: HttpErrorResponse) => {
        console.error(err);
        let errorMessage = 'Ocurrió un error al registrar.';
        if (err.status === 422 && err.error.errors.email) {
          errorMessage = 'El correo electrónico ya está en uso.';
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