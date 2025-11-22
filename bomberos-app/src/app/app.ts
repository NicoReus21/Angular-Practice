import { Component, signal, OnInit } from '@angular/core';
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
export class AppComponent implements OnInit {
  title = 'bomberos-app';
  showBackButton = signal(false);
  isAuthPage = signal(false); 

  readonly LOGIN_ROUTE = '/login'; 
  readonly REGISTER_ROUTE = '/register';
  readonly RECOVER_ROUTE = '/recover-password';

  readonly ROUTES_WITH_BACK_BUTTON = [
    '/historial', 
    '/machine-historial', 
    '/rols', 
    '/document-upload'
  ];

  constructor(private router: Router) {}

  ngOnInit() {
    this.router.events.pipe(
      filter((event): event is NavigationEnd => event instanceof NavigationEnd)
    ).subscribe((event: NavigationEnd) => {
      const currentUrl = event.urlAfterRedirects;

      const shouldShowBack = this.ROUTES_WITH_BACK_BUTTON.some(route => currentUrl.startsWith(route));
      this.showBackButton.set(shouldShowBack);

      const isAuth = currentUrl.startsWith(this.LOGIN_ROUTE) || currentUrl.startsWith(this.REGISTER_ROUTE) ||currentUrl.startsWith(this.RECOVER_ROUTE);
      
      this.isAuthPage.set(isAuth);
    });
  }
}