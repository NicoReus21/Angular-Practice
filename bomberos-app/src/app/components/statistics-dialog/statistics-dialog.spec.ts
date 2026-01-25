import { ComponentFixture, TestBed } from '@angular/core/testing';
import { StatisticsDialogComponent } from './statistics-dialog';
import { MachineHistorialService } from '../../services/machine-historial';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { of } from 'rxjs';

describe('StatisticsDialogComponent', () => {
  let component: StatisticsDialogComponent;
  let fixture: ComponentFixture<StatisticsDialogComponent>;

  const mockService = {
    getAnnualBudget: jasmine.createSpy('getAnnualBudget').and.returnValue(of({ year: 2025, amount: 100000 })),
    saveAnnualBudget: jasmine.createSpy('saveAnnualBudget').and.returnValue(of({ year: 2025, amount: 200000 }))
  };

  const mockDialogRef = {
    close: jasmine.createSpy('close')
  };

  const mockDialogData = {
    units: [] 
  };

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [StatisticsDialogComponent], 
      providers: [
        { provide: MachineHistorialService, useValue: mockService },
        { provide: MatDialogRef, useValue: mockDialogRef },
        { provide: MAT_DIALOG_DATA, useValue: mockDialogData }
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(StatisticsDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges(); 
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should call getAnnualBudget on init', () => {
    expect(mockService.getAnnualBudget).toHaveBeenCalled();
  });
});