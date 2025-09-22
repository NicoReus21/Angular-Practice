import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FileUploadService {
  private backendUrl = '';

  constructor(private http: HttpClient) { }
  
  uploadFile(file: File, stepTitle: string): Observable<any> {

    const formData = new FormData();
    formData.append('documento', file, file.name)
    formData.append('step', stepTitle);
    return this.http.post(this.backendUrl, formData);
  }
}
