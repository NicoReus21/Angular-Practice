import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login';
import { HistorialComponent } from './components/historial/historial';
import { DocumentUploadComponent } from './components/document-upload/document-upload';
import { MachineHistorialComponent } from './components/machine-historial/machine-historial';
import { CreateFiretruckComponent } from './components/create-firetruck/create-firetruck';


export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'historial', component: HistorialComponent },
  { path: 'document-upload', component: DocumentUploadComponent },
  { path: 'document-upload/:id', component: DocumentUploadComponent },
  { path: 'machine-historial', component: MachineHistorialComponent },
  { path: 'create-firetruck', component: CreateFiretruckComponent }, // <-- AÑADIDO
];
