import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface ApiRole {
  id: number;
  name: string;
}

export interface ApiGroup {
  id: number;
  name: string;
  description?: string | null;
  users_count?: number;
  created_at?: string;
}

export interface ApiUser {
  id: number;
  name: string;
  email: string;
  company?: string | null;
  status?: string | null;
  roles?: ApiRole[];
  groups?: ApiGroup[];
}

@Injectable({
  providedIn: 'root'
})
export class AuthDirectoryService {
  private apiUrl = 'http://127.0.0.1:8000/api';
  private http = inject(HttpClient);

  getUsers(): Observable<ApiUser[]> {
    return this.http.get<ApiUser[]>(`${this.apiUrl}/users`);
  }

  getGroups(): Observable<ApiGroup[]> {
    return this.http.get<ApiGroup[]>(`${this.apiUrl}/groups`);
  }

  createGroup(payload: { name: string; description?: string | null }): Observable<ApiGroup> {
    return this.http.post<ApiGroup>(`${this.apiUrl}/groups`, payload);
  }

  updateGroup(groupId: number, payload: { name?: string; description?: string | null }): Observable<ApiGroup> {
    return this.http.put<ApiGroup>(`${this.apiUrl}/groups/${groupId}`, payload);
  }

  assignUserToGroup(userId: number, groupId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/groups/${groupId}`, {});
  }

  assignPermissionToGroup(groupId: number, permissionId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/groups/${groupId}/permissions/${permissionId}`, {});
  }

  assignGroupsToUser(userId: number, groupIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/groups`, { groups: groupIds });
  }

  assignRolesToUser(userId: number, roleIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/roles`, { roles: roleIds });
  }
}
