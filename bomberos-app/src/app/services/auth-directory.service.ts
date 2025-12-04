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
}
