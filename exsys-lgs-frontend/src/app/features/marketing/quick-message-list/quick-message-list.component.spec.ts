import { ComponentFixture, TestBed } from '@angular/core/testing';

import { QuickMessageListComponent } from './quick-message-list.component';

describe('QuickMessageListComponent', () => {
  let component: QuickMessageListComponent;
  let fixture: ComponentFixture<QuickMessageListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [QuickMessageListComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(QuickMessageListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
