import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateFiretruckComponent } from './create-firetruck';

describe('CreateFiretruckComponent', () => {
  let component: CreateFiretruckComponent;
  let fixture: ComponentFixture<CreateFiretruckComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateFiretruckComponent]
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
