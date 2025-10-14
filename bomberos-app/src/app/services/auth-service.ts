import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient, private router: Router) { }

  login(credentials: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/login`, credentials).pipe(
      tap(response => {
        if (response && response.token) {

          this.saveToken(response.token);
        }
      })
    );
  }


  saveToken(token: string): void {
    localStorage.setItem('authToken', token);
  }

  getToken(): string | null {
    return localStorage.getItem('authToken');
  }


  logout(): void {
    // Para un logout completo, deberíamos llamar al endpoint /api/logout
    // pero por ahora, simplemente borramos el token y redirigimos.
    localStorage.removeItem('authToken');
    this.router.navigate(['/']); // Redirige a la página de login o inicio
  }
}

