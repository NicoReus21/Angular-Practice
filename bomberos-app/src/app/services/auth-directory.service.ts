import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

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

export interface ApiPermission {
  id: number;
  module: string;
  section: string;
  action: string;
  description?: string | null;
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
  private apiUrl = environment.apiUrl;
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

  getUser(userId: number): Observable<ApiUser> {
    return this.http.get<ApiUser>(`${this.apiUrl}/users/${userId}`);
  }

  getGroup(groupId: number): Observable<ApiGroup> {
    return this.http.get<ApiGroup>(`${this.apiUrl}/groups/${groupId}`);
  }

  getUserGroups(userId: number): Observable<ApiGroup[]> {
    return this.http.get<ApiGroup[]>(`${this.apiUrl}/users/${userId}/groups`);
  }

  getGroupUsers(groupId: number): Observable<ApiUser[]> {
    return this.http.get<ApiUser[]>(`${this.apiUrl}/groups/${groupId}/users`);
  }

  getUserRoles(userId: number): Observable<ApiRole[]> {
    return this.http.get<ApiRole[]>(`${this.apiUrl}/users/${userId}/roles`);
  }

  getPermissions(): Observable<ApiPermission[]> {
    return this.http.get<ApiPermission[]>(`${this.apiUrl}/permissions`);
  }

  getGroupPermissions(groupId: number): Observable<ApiPermission[]> {
    return this.http.get<ApiPermission[]>(`${this.apiUrl}/groups/${groupId}/permissions`);
  }

  getCurrentUserPermissions(): Observable<ApiPermission[]> {
    return this.http.get<ApiPermission[]>(`${this.apiUrl}/user/permissions`);
  }

  getCurrentUser(): Observable<ApiUser> {
    return this.http.get<ApiUser>(`${this.apiUrl}/user`);
  }

  updateGroup(groupId: number, payload: { name?: string; description?: string | null }): Observable<ApiGroup> {
    return this.http.put<ApiGroup>(`${this.apiUrl}/groups/${groupId}`, payload);
  }

  assignUserToGroup(userId: number, groupId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/groups/${groupId}`, {});
  }

  removeUserFromGroup(userId: number, groupId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/users/${userId}/groups/${groupId}`);
  }

  assignPermissionToGroup(groupId: number, permissionId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/groups/${groupId}/permissions/${permissionId}`, {});
  }

  revokePermissionFromGroup(groupId: number, permissionId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/groups/${groupId}/permissions/${permissionId}`);
  }

  assignGroupsToUser(userId: number, groupIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/groups`, { groups: groupIds });
  }

  assignRolesToUser(userId: number, roleIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/roles`, { roles: roleIds });
  }

  assignRoleToUser(userId: number, roleId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/users/${userId}/roles/${roleId}`, {});
  }

  removeRoleFromUser(userId: number, roleId: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/users/${userId}/roles/${roleId}`);
  }
}
