import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { Router } from '@angular/router';
import { environment } from '../../environments/environment';
import { PermissionStoreService } from './permission-store.service';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private apiUrl = environment.apiUrl;

  constructor(
    private http: HttpClient,
    private router: Router,
    private permissionStore: PermissionStoreService
  ) {}

  login(credentials: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/login`, credentials).pipe(
      tap(response => {
        if (response && response.token) {
          this.saveToken(response.token);
        }
      })
    );
  }

  googleLogin(idToken: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/google-login`, { id_token: idToken }).pipe(
      tap(response => {
        if (response && response.token) {
          this.saveToken(response.token);
        }
      })
    );
  }

  register(name: string, email: string, password: string): Observable<any> {
    const userCredentials = { name, email, password };

    return this.http.post<any>(`${this.apiUrl}/register`, userCredentials).pipe(
      tap(response => {
        if (response && response.token) {
          this.saveToken(response.token);
        }
      })
    );
  }

  recoverPassword(email: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/recover-password`, { email });
  }

  changePassword(
    currentPassword: string,
    newPassword: string,
    newPasswordConfirmation: string
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/change-password`, {
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: newPasswordConfirmation
    });
  }

  saveToken(token: string): void {
    localStorage.setItem('authToken', token);
    this.permissionStore.clear();
  }

  getToken(): string | null {
    return localStorage.getItem('authToken');
  }

  logout(): void {
    localStorage.removeItem('authToken');
    this.permissionStore.clear();
    this.router.navigate(['/login']);
  }
}
