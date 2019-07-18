import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SimViewComponent } from './sim-view.component';

describe('SimViewComponent', () => {
  let component: SimViewComponent;
  let fixture: ComponentFixture<SimViewComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SimViewComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SimViewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
