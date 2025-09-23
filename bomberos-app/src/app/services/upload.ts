import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { UploadSection } from '../components/document-upload/document-upload';

@Injectable({
  providedIn: 'root'
})
export class FileUploadService {

  private backendUrl = '';

  private http = inject(HttpClient);

  submitFullProcess(preliminaryData: { bomberoNombre: string, compania: string }, sections: UploadSection[]): Observable<any> {
    const formData = new FormData();

    formData.append('bombero_nombre', preliminaryData.bomberoNombre);
    formData.append('compania', preliminaryData.compania);

    const sectionsMetadata = sections.map(section => ({
      title: section.title,
      steps: section.steps.map(step => ({
        title: step.title,
        optional: step.optional,
        isCompleted: step.isCompleted,
        isPaid: step.isPaid,
        fileNames: step.files.map(f => f.name) 
      }))
    }));
    formData.append('sections_data', JSON.stringify(sectionsMetadata));
    sections.forEach((section, sectionIndex) => {
      section.steps.forEach((step, stepIndex) => {
        step.files.forEach((file) => {
          const key = `files[${sectionIndex}][${stepIndex}][]`;
          formData.append(key, file, file.name);
        });
      });
    });
    return this.http.post(this.backendUrl, formData);
  }
}
