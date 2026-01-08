import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

import { CreateFiretruckComponent } from './create-firetruck';

describe('CreateFiretruckComponent', () => {
  let component: CreateFiretruckComponent;
  let fixture: ComponentFixture<CreateFiretruckComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateFiretruckComponent],
      providers: [
        { provide: MatDialogRef, useValue: { close: () => {} } },
        { provide: MAT_DIALOG_DATA, useValue: { unit: null } },
      ],
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreateFiretruckComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
