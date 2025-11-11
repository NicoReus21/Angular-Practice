import { TestBed } from '@angular/core/testing';

import { MachineHistorialService } from './machine-historial';

describe('MachineHistorial', () => {
  let service: MachineHistorialService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(MachineHistorialService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
