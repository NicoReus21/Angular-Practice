import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RoleService {
  private apiUrl = 'http://127.0.0.1:8000/api';
  private http = inject(HttpClient);

  getRoles(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/rols`);
  }

  createRole(name: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/rols`, { name });
  }

  deleteRole(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/rols/${id}`);
  }


  getAllPermissions(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/permissions`);
  }


  getRolePermissions(roleId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/rols/${roleId}/permissions`);
  }

  syncRolePermissions(roleId: number, permissionIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/rols/${roleId}/permissions`, {
      permissions: permissionIds 
    });
  }
}