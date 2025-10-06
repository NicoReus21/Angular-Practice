import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
// 1. Importar librerías de animación
import { trigger, transition, style, animate } from '@angular/animations'; 

@Component({
  selector: 'app-login',
  standalone: true, 
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule
  ],
  templateUrl: './login.html', // Corregido: asumimos el nombre estándar .component.html
  styleUrls: ['./login.scss'],
  // 2. Añadir el trigger de animación
  animations: [
    trigger('slideIn', [
      transition(':enter', [
        style({ transform: 'translateX(100%)', opacity: 0 }), 
        animate('600ms ease-out', style({ transform: 'translateX(0)', opacity: 1 }))
      ])
    ])
  ]
})

export class LoginComponent {
  // Ajuste: Cambiamos 'username' a 'email' en el FormGroup para mayor claridad y consistencia
  loginForm: FormGroup;
  errorMessage: string | null = null;

  constructor(private fb: FormBuilder, private router: Router) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]], // Usamos email y añadimos validación de email
      password: ['', Validators.required]
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      // Ajuste: Usar 'email' en lugar de 'username'
      const { email, password } = this.loginForm.value; 
      if (email === 'admin@tuemail.com' && password === '123') { // Ejemplo con email
        this.router.navigate(['/historial']);
      } else {
        this.errorMessage = 'Email o contraseña incorrectos.';
      }
    } else {
        // Asegurar que todos los campos se marquen como tocados para mostrar errores.
        this.loginForm.markAllAsTouched();
    }
  }

  // Métodos getter para acceder a los controles fácilmente en el HTML
  get emailControl() {
    return this.loginForm.get('email');
  }
}