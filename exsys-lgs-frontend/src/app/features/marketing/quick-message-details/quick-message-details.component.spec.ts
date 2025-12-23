import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuickMessageDetailsComponent } from './quick-message-details.component';

describe('QuickMessageDetailsComponent', () => {
  let component: QuickMessageDetailsComponent;
  let fixture: ComponentFixture<QuickMessageDetailsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [QuickMessageDetailsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuickMessageDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
