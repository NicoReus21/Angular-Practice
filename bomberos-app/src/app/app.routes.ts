import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login';
import { HistorialComponent } from './components/historial/historial';
import { DocumentUploadComponent } from './components/document-upload/document-upload';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'historial', component: HistorialComponent },
  { path: 'app', component: DocumentUploadComponent },
  { path: 'app/:id', component: DocumentUploadComponent },
];
