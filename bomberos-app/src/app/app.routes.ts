import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login';
import { HistorialComponent } from './components/historial/historial';
import { DocumentUploadComponent } from './components/document-upload/document-upload';
import { MachineHistorialComponent } from './components/machine-historial/machine-historial';
import { CreateFiretruckComponent } from './components/create-firetruck/create-firetruck';
import { UserRegisterComponent } from './components/user-register/user-register';
import { RoleManagementComponent } from './components/role-management/role-management';
import { RecoverPasswordComponent } from './components/recover-user-password/recover-user-password';
import { AuthDashboardComponent } from './components/auth-dashboard/auth-dashboard';
import { ModulesOverviewComponent } from './components/modules-overview/modules-overview';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'modules', component: ModulesOverviewComponent },
  { path: 'app', redirectTo: 'modules', pathMatch: 'full' },
  { path: 'historial', component: HistorialComponent },
  { path: 'document-upload', component: DocumentUploadComponent },
  { path: 'document-upload/:id', component: DocumentUploadComponent },
  { path: 'machine-historial', component: MachineHistorialComponent },
  { path: 'create-firetruck', component: CreateFiretruckComponent }, 
  {path: 'register', component: UserRegisterComponent },
  //{ path: 'rols', component: RoleManagementComponent },
  { path: 'recover-password', component: RecoverPasswordComponent },
  { path: 'rols', component: AuthDashboardComponent },




  /*TODO, CUANDO SEPA BIEN LOS ROLES QUE SE LLEVARAN ESTO SERIA ASI:
  {path: 'admin',
      children: [
        { path: 'register', component: UserRegisterComponent },
        { path: 'roles', component: RoleManagementComponent },
        { path: 'grupos', component: GruposComponent },
      ]
    },
  */
];
