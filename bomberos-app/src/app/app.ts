import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-root',
  standalone: true,
  // ¡Y añádelo al array de imports!
  imports: [CommonModule, RouterOutlet],
  templateUrl: './app.html',
  styleUrls: ['./app.scss']
})
export class AppComponent {
  title = 'bomberos-app';
}