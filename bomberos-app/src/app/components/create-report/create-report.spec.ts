import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

import { CreateReportComponent } from './create-report';

describe('CreateReportComponent', () => {
  let component: CreateReportComponent;
  let fixture: ComponentFixture<CreateReportComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateReportComponent],
      providers: [
        { provide: MatDialogRef, useValue: { close: () => {} } },
        {
          provide: MAT_DIALOG_DATA,
          useValue: {
            unit: { id: 0, model: null, plate: '', company: '', documents: [] },
            editMode: false,
          },
        },
      ],
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreateReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
