import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';

import { RecoverPasswordComponent } from './recover-user-password';

describe('RecoverPasswordComponent', () => {
  let component: RecoverPasswordComponent;
  let fixture: ComponentFixture<RecoverPasswordComponent>;
  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HttpClientTestingModule, RecoverPasswordComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RecoverPasswordComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
