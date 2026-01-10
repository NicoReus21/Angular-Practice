import { Injectable, inject } from '@angular/core';
import { Observable, of } from 'rxjs';
import { finalize, map, shareReplay, tap } from 'rxjs/operators';
import { AuthDirectoryService, ApiPermission } from './auth-directory.service';

@Injectable({
  providedIn: 'root'
})
export class PermissionStoreService {
  private authDirectory = inject(AuthDirectoryService);
  private cachedPermissions: ApiPermission[] | null = null;
  private inflight$: Observable<ApiPermission[]> | null = null;

  load(): Observable<ApiPermission[]> {
    if (this.cachedPermissions) {
      return of(this.cachedPermissions);
    }

    if (this.inflight$) {
      return this.inflight$;
    }

    this.inflight$ = this.authDirectory.getCurrentUserPermissions().pipe(
      map((permissions) => permissions || []),
      tap((permissions) => {
        this.cachedPermissions = permissions;
      }),
      finalize(() => {
        this.inflight$ = null;
      }),
      shareReplay(1)
    );

    return this.inflight$;
  }

  clear(): void {
    this.cachedPermissions = null;
    this.inflight$ = null;
  }
}
