import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RevenueImpactComponent } from './revenue-impact.component';

describe('RevenueImpactComponent', () => {
  let component: RevenueImpactComponent;
  let fixture: ComponentFixture<RevenueImpactComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [RevenueImpactComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RevenueImpactComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
