import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MarketingActionFormComponent } from './marketing-action-form.component';

describe('MarketingActionFormComponent', () => {
  let component: MarketingActionFormComponent;
  let fixture: ComponentFixture<MarketingActionFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [MarketingActionFormComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MarketingActionFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
