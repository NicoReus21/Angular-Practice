import { HttpErrorResponse, HttpEvent, HttpHandlerFn, HttpInterceptorFn, HttpRequest } from '@angular/common/http';
import { inject } from '@angular/core';
import { MatSnackBar, MatSnackBarRef } from '@angular/material/snack-bar';
import { catchError, Observable, throwError } from 'rxjs';
import { ForbiddenSnackbarComponent } from '../components/forbidden-snackbar/forbidden-snackbar';

let activeForbiddenRef: MatSnackBarRef<ForbiddenSnackbarComponent> | null = null;

export const forbiddenInterceptor: HttpInterceptorFn = (
  req: HttpRequest<any>,
  next: HttpHandlerFn
): Observable<HttpEvent<any>> => {
  const snackBar = inject(MatSnackBar);

  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 403) {
        if (!activeForbiddenRef) {
          activeForbiddenRef = snackBar.openFromComponent(ForbiddenSnackbarComponent, {
            horizontalPosition: 'center',
            verticalPosition: 'top',
            panelClass: ['forbidden-snackbar'],
            data: {
              message: 'No tienes permisos para realizar esta accion.',
              detail: 'Contacte a su administrador si esto cree que es un error.'
            }
          });

          activeForbiddenRef.afterDismissed().subscribe(() => {
            activeForbiddenRef = null;
          });
        }
      }

      return throwError(() => error);
    })
  );
};
