import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MachineHistorialComponent } from './machine-historial';

describe('MachineHistorial', () => {
  let component: MachineHistorialComponent;
  let fixture: ComponentFixture<MachineHistorialComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MachineHistorialComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MachineHistorialComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
