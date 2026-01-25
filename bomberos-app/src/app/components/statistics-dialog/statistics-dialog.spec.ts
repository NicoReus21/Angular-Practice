import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StatisticsDialogComponent } from './statistics-dialog';

describe('StatisticsDialog', () => {
  let component: StatisticsDialogComponent;
  let fixture: ComponentFixture<StatisticsDialogComponent>;
  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [StatisticsDialogComponent],
    })
    .compileComponents();

    fixture = TestBed.createComponent(StatisticsDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
