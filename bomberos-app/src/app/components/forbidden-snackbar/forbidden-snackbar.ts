import { CommonModule } from '@angular/common';
import { Component, Inject } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MAT_SNACK_BAR_DATA, MatSnackBarRef } from '@angular/material/snack-bar';

@Component({
  selector: 'app-forbidden-snackbar',
  standalone: true,
  imports: [CommonModule, MatButtonModule],
  templateUrl: './forbidden-snackbar.html',
  styleUrl: './forbidden-snackbar.scss'
})
export class ForbiddenSnackbarComponent {
  constructor(
    public snackBarRef: MatSnackBarRef<ForbiddenSnackbarComponent>,
    @Inject(MAT_SNACK_BAR_DATA)
    public data: { message: string; detail: string }
  ) {}
}
