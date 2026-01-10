import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { catchError, map, of } from 'rxjs';
import { AuthService } from '../services/auth-service';
import { PermissionStoreService } from '../services/permission-store.service';

const hasPermission = (permissions: string[], rule: string): boolean => {
  const rules = rule.split('|').map((value) => value.trim()).filter(Boolean);
  return rules.some((candidate) => permissions.includes(candidate));
};

export const permissionGuard = (rule: string): CanActivateFn => {
  return () => {
    const router = inject(Router);
    const authService = inject(AuthService);
    const permissionStore = inject(PermissionStoreService);

    if (!authService.getToken()) {
      return router.parseUrl('/login');
    }

    return permissionStore.load().pipe(
      map((permissions) => {
        const permissionKeys = permissions.map(
          (permission) => `${permission.module}:${permission.section}:${permission.action}`
        );

        return hasPermission(permissionKeys, rule)
          ? true
          : router.parseUrl('/landing');
      }),
      catchError((error: HttpErrorResponse) => {
        if (error.status === 401) {
          return of(router.parseUrl('/login'));
        }
        return of(router.parseUrl('/landing'));
      })
    );
  };
};
