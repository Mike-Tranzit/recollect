import { Component, OnInit } from '@angular/core';
import { BalanceService } from '../../_services/balance.service';
import { Balance } from  '../../_class/balance';

@Component({
  selector: 'app-balance',
  templateUrl: './balance.component.html',
  styleUrls: ['./balance.component.css'],
  providers: [BalanceService]
})
export class BalanceComponent implements OnInit {

  public visible = false;
  public visibleAnimate = false;
  public payments: Balance[] = [];
  public terminal: any = {
    '=1': 'Друг',
    'other': 'Не друг',
  };
  constructor(private balanceApp: BalanceService) {

  }

  ngOnInit() {

  }
  public show(plate: string): void {
    this.balanceApp.getBalance(plate).then( ( resp: Balance[] ) => {
      this.payments = resp;
      this.visible = true;
      setTimeout(() => this.visibleAnimate = true, 100);
    });
  }

  public hide(): void {
    this.visibleAnimate = false;
    setTimeout(() => this.visible = false, 300);
  }

  public onContainerClicked(event: MouseEvent): void {
    if ((<HTMLElement>event.target).classList.contains('modal')) {

      this.hide();
    }
  }
}
