import { RecollectPage } from './app.po';

describe('recollect App', function() {
  let page: RecollectPage;

  beforeEach(() => {
    page = new RecollectPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
