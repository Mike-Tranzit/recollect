import { TestBed, async, inject } from '@angular/core/testing';

import { ListViewGuard } from './list-view.guard';

describe('ListViewGuard', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ListViewGuard]
    });
  });

  it('should ...', inject([ListViewGuard], (guard: ListViewGuard) => {
    expect(guard).toBeTruthy();
  }));
});
