import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuickMessageFormComponent } from './quick-message-form.component';

describe('QuickMessageFormComponent', () => {
  let component: QuickMessageFormComponent;
  let fixture: ComponentFixture<QuickMessageFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [QuickMessageFormComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuickMessageFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
