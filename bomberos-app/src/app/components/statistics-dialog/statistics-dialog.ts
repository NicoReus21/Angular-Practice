import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { CommonModule, CurrencyPipe } from '@angular/common';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { MatDividerModule } from '@angular/material/divider';
import { MatTooltipModule } from '@angular/material/tooltip';
import Swal from 'sweetalert2';

import { MachineHistorialService, VehicleUnit, AttachedDocument } from '../../services/machine-historial';

@Component({
  selector: 'app-statistics-dialog',
  standalone: true,
  imports: [
    CommonModule,
    MatDialogModule,
    MatButtonModule,
    MatIconModule,
    MatProgressBarModule,
    MatDividerModule,
    MatTooltipModule,
    CurrencyPipe,
  ],
  templateUrl: './statistics-dialog.html',
  styleUrls: ['./statistics-dialog.scss'],
})
export class StatisticsDialogComponent implements OnInit {
  private service = inject(MachineHistorialService);
  private dialogRef = inject(MatDialogRef<StatisticsDialogComponent>);
  private data = inject<{ units: VehicleUnit[] }>(MAT_DIALOG_DATA);

  unitsSignal = signal<VehicleUnit[]>(this.data.units || []);
  annualBudgetSignal = signal<{ year: number; amount: number }>({
    year: new Date().getFullYear(),
    amount: 0,
  });

  totalUnitsCount = computed(() => this.unitsSignal().length);

  serviceUnitsCount = computed(
    () => this.unitsSignal().filter((u) => u.status === 'En Servicio').length,
  );

  availabilityPercentage = computed(() => {
    const total = this.totalUnitsCount();
    const service = this.serviceUnitsCount();
    return total === 0 ? 0 : Math.round((service / total) * 100);
  });

  globalBudgetMetrics = computed(() => {
    const budgetData = this.annualBudgetSignal();
    const budgetYear = budgetData.year;
    const budgetAmount = budgetData.amount;
    const units = this.unitsSignal();

    let totalSpentGlobal = 0;

    units.forEach((unit) => {
      if (unit.documents) {
        const unitYearlyExpenses = unit.documents
          .filter((doc: AttachedDocument) => { // Tipado explícito aquí
            const docDate = new Date(doc.created_at_raw);
            return docDate.getFullYear() === budgetYear;
          })
          .reduce((sum, doc) => sum + (doc.cost || 0), 0);
        totalSpentGlobal += unitYearlyExpenses;
      }
    });

    const remaining = budgetAmount - totalSpentGlobal;
    const percentageUsed = budgetAmount > 0 ? (totalSpentGlobal / budgetAmount) * 100 : 0;

    return {
      year: budgetYear,
      budget: budgetAmount,
      totalSpent: totalSpentGlobal,
      remaining: remaining,
      percentageUsed: percentageUsed,
      isOverBudget: totalSpentGlobal > budgetAmount,
    };
  });

  ngOnInit() {
    this.loadBudget();
  }

  loadBudget() {
    const currentYear = new Date().getFullYear();
    this.service.getAnnualBudget(currentYear).subscribe({
      next: (data) => this.annualBudgetSignal.set({ year: data.year, amount: Number(data.amount) }),
      error: (e) => console.error(e),
    });
  }

  openEditBudgetDialog() {
    const metrics = this.globalBudgetMetrics();
    Swal.fire({
      title: `Presupuesto Global ${metrics.year}`,
      text: 'Define el monto total anual para la flota.',
      input: 'number',
      inputValue: metrics.budget,
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      inputValidator: (val) => (!val || Number(val) < 0 ? 'Monto inválido' : null),
    }).then((res) => {
      if (res.isConfirmed) {
        const newAmount = Number(res.value);
        this.service.saveAnnualBudget(metrics.year, newAmount).subscribe({
          next: (saved) => {
            this.annualBudgetSignal.set({ year: saved.year, amount: Number(saved.amount) });
            Swal.fire('Guardado', 'Presupuesto actualizado', 'success');
          },
        });
      }
    });
  }

  close() {
    this.dialogRef.close();
  }
}