import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

import { EditRoleDialogComponent } from './edit-role-dialog';

describe('EditRoleDialogComponent', () => {
  let component: EditRoleDialogComponent;
  let fixture: ComponentFixture<EditRoleDialogComponent>;
  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HttpClientTestingModule, EditRoleDialogComponent],
      providers: [
        { provide: MatDialogRef, useValue: { close: () => {} } },
        { provide: MAT_DIALOG_DATA, useValue: { role: { id: 0, name: '' } } },
      ],
    })
    .compileComponents();

    fixture = TestBed.createComponent(EditRoleDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
