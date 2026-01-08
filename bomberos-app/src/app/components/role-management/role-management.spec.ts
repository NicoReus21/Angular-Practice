import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';

import { RoleManagementComponent } from './role-management';

describe('RoleManagement', () => {
  let component: RoleManagementComponent;
  let fixture: ComponentFixture<RoleManagementComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HttpClientTestingModule, RoleManagementComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RoleManagementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
