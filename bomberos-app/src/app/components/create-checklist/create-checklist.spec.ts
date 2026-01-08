import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

import { CreateChecklistComponent } from './create-checklist';

describe('CreateChecklistComponent', () => {
  let component: CreateChecklistComponent;
  let fixture: ComponentFixture<CreateChecklistComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateChecklistComponent],
      providers: [
        { provide: MatDialogRef, useValue: { close: () => {} } },
        { provide: MAT_DIALOG_DATA, useValue: { editMode: false, checklist: null } },
      ],
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreateChecklistComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
