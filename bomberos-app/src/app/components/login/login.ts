import { Component, OnInit, AfterViewInit, ElementRef, ViewChild, NgZone } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon'; // <--- 1. IMPORTAR ESTO
import { trigger, transition, style, animate } from '@angular/animations';
import { AuthService } from '../../services/auth-service'; 
import { HttpClientModule } from '@angular/common/http'; 
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner'; // Necesario para el spinner en el HTML
import { environment } from '../../../environments/environment';

declare global {
  interface Window {
    google?: any;
  }
}

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule, // <--- 2. AGREGARLO AQUÍ
    HttpClientModule,
    MatProgressSpinnerModule, // Aseguramos que el spinner funcione si se usa en el HTML
    RouterLink,
  ],
  templateUrl: './login.html',
  styleUrls: ['./login.scss'],
  animations: [
    trigger('slideIn', [
      transition(':enter', [
        style({ transform: 'translateX(100%)', opacity: 0 }),
        animate('600ms ease-out', style({ transform: 'translateX(0)', opacity: 1 }))
      ])
    ])
  ]
})
export class LoginComponent implements OnInit, AfterViewInit {
  loginForm: FormGroup;
  errorMessage: string | null = null;
  isLoading = false;
  readonly hasGoogleClientId = !!environment.googleClientId;
  private googleLoaded = false;

  @ViewChild('googleButton') googleButton?: ElementRef<HTMLDivElement>;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private authService: AuthService,
    private ngZone: NgZone
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    const token = this.authService.getToken();
    if (token) {
      this.router.navigate(['/modules']);
    }

  }

  ngAfterViewInit(): void {
    if (this.hasGoogleClientId) {
      this.loadGoogleIdentity();
    }
  }

  onSubmit(): void {
    if (this.loginForm.invalid) {
      this.loginForm.markAllAsTouched();
      return;
    }

    this.isLoading = true;
    this.errorMessage = null;

    this.authService.login(this.loginForm.value).subscribe({
      next: (response) => {
        console.log('Login exitoso!', response);
        this.router.navigate(['/auth-transition']);
      },
      error: (err) => {
        console.error('Error en el login:', err);
        this.errorMessage = 'Email o contraseña incorrectos.';
        this.isLoading = false;
      },
      complete: () => {
        this.isLoading = false;
      }
    });
  }

  get emailControl() {
    return this.loginForm.get('email');
  }

  private loadGoogleIdentity(): void {
    if (!environment.googleClientId) {
      return;
    }

    if (window.google?.accounts?.id) {
      this.initializeGoogle();
      return;
    }

    if (this.googleLoaded) {
      return;
    }

    this.googleLoaded = true;
    const script = document.createElement('script');
    script.src = 'https://accounts.google.com/gsi/client';
    script.async = true;
    script.defer = true;
    script.onload = () => this.initializeGoogle();
    script.onerror = () => {
      console.error('No se pudo cargar Google Identity Services.');
    };
    document.head.appendChild(script);
  }

  private initializeGoogle(): void {
    if (!window.google?.accounts?.id || !environment.googleClientId || !this.googleButton?.nativeElement) {
      return;
    }

    window.google.accounts.id.initialize({
      client_id: environment.googleClientId,
      callback: (response: { credential?: string }) => this.onGoogleCredential(response)
    });

    window.google.accounts.id.renderButton(this.googleButton.nativeElement, {
      theme: 'outline',
      size: 'large',
      text: 'continue_with',
      shape: 'rectangular',
      logo_alignment: 'left'
    });
  }

  private onGoogleCredential(response: { credential?: string }): void {
    const credential = response?.credential;
    if (!credential) {
      this.errorMessage = 'No se pudo autenticar con Google.';
      return;
    }

    this.isLoading = true;
    this.errorMessage = null;

    this.authService.googleLogin(credential).subscribe({
      next: () => {
        this.ngZone.run(() => this.router.navigate(['/auth-transition']));
      },
      error: (err) => {
        console.error('Error en login Google:', err);
        this.errorMessage = 'No se pudo iniciar sesion con Google.';
        this.isLoading = false;
      },
      complete: () => {
        this.isLoading = false;
      }
    });
  }
}
