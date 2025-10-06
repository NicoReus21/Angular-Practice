import { Component, signal, OnInit } from '@angular/core'; // 1. Añadir OnInit
import { CommonModule } from '@angular/common';
import { NavigationEnd, Router, RouterLink, RouterOutlet } from '@angular/router';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { filter } from 'rxjs';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    RouterOutlet,
    RouterLink,
    MatToolbarModule,
    MatButtonModule,
    MatIconModule
  ],
  templateUrl: './app.html',
  styleUrls: ['./app.scss']
})
export class AppComponent implements OnInit { // 2. Implementar OnInit
  title = 'bomberos-app';
  showBackButton = signal(false);
  // 3. Nueva señal para el estado de la página de Login
  isLoginPage = signal(false); 
  
  // 4. Definir la ruta de login para fácil mantenimiento
  readonly LOGIN_ROUTE = '/login'; 

  constructor(private router: Router) {}

  ngOnInit() {
    this.router.events.pipe(
      filter((event): event is NavigationEnd => event instanceof NavigationEnd)
    ).subscribe((event: NavigationEnd) => {
      
      // 5. Lógica para el botón de retroceso (usando tu lógica original)
      this.showBackButton.set(event.urlAfterRedirects === '/app');
      
      // 6. Lógica para el Login: Comprueba si la ruta actual es la de login
      //    Usamos .startsWith() para incluir posibles query params, aunque includes() también serviría.
      const isLogin = event.urlAfterRedirects.startsWith(this.LOGIN_ROUTE);
      this.isLoginPage.set(isLogin);
    });
  }
}