import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login';
import { HistorialComponent } from './components/historial/historial';
import { DocumentUploadComponent } from './components/document-upload/document-upload';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'historial', component: HistorialComponent },
  { path: 'document-upload', component: DocumentUploadComponent },
  { path: 'document-upload/:id', component: DocumentUploadComponent },
];
