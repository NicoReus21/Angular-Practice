import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateChecklist } from './create-checklist';

describe('CreateChecklist', () => {
  let component: CreateChecklist;
  let fixture: ComponentFixture<CreateChecklist>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateChecklist]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreateChecklist);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
