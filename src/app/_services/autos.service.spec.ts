import { TestBed, inject } from '@angular/core/testing';

import { AutosService } from './autos.service';

describe('AutosService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [AutosService]
    });
  });

  it('should be created', inject([AutosService], (service: AutosService) => {
    expect(service).toBeTruthy();
  }));
});
