import { HttpEvent, HttpHandlerFn, HttpInterceptorFn, HttpRequest } from '@angular/common/http';
import { inject } from '@angular/core';
import { Observable } from 'rxjs';
import { AuthService } from '../services/auth-service'; // Asegúrate que la ruta sea correcta

/**
 * Este es un interceptor funcional, el formato moderno que espera `withInterceptors`.
 * Ya no es una clase, sino una función constante.
 */
export const authInterceptor: HttpInterceptorFn = (
  req: HttpRequest<any>,
  next: HttpHandlerFn
): Observable<HttpEvent<any>> => {
  // Usamos inject() para obtener dependencias, en lugar de un constructor.
  const authService = inject(AuthService);
  const authToken = authService.getToken();

  // La lógica interna es exactamente la misma.
  if (authToken) {
    const authReq = req.clone({
      setHeaders: {
        Authorization: `Bearer ${authToken}`,
      },
    });
    return next(authReq);
  }

  return next(req);
};

