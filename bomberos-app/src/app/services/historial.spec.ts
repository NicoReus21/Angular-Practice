import { TestBed } from '@angular/core/testing';

import { HistorialService } from './historial';

describe('Historial', () => {
  let service: HistorialService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(HistorialService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
