import { Component, OnInit, OnDestroy, ViewChild } from '@angular/core';
import { DataService } from '../../_services/data.service';
import { Glonass } from '../../_class/glonass';
import { Subscription } from 'rxjs/Subscription';
import { TrelloService } from  '../../_services/trello.service';
import { ModalComponent } from '../modal/modal.component';
import { BalanceComponent } from '../balance/balance.component';

declare var Favico: any;

@Component({
  selector: 'app-main',
  templateUrl: 'main.component.html',
  styleUrls: ['main.component.css'],
  providers: [DataService]
})
export class MainComponent implements OnInit, OnDestroy {

  public data: Glonass[] = [];

  private plates: Array<string> = [];
  public item: Glonass = null;
  public trelloList: any = [];
  public highlightedDiv: number;
  public timeslotsData: Glonass[] = [];

  private subscribeData: Subscription;
  private subscribeDataTimeslots: Subscription;
  private subscribeDelete: Subscription;
  private subscribeTrello: Subscription;
  private subscribeTrelloList: Subscription;

  @ViewChild('modal') public modalApp: ModalComponent;
  @ViewChild('balance') public balanceApp: BalanceComponent;

  public countStartData: any = null;
  public statusTitle: any = {
  '=1': 'На НАТ',
  '=2': 'Ушел с НАТ',
  '=3': 'На НЗТ',
  '=4': 'Разгружен',
  'other': 'Не понятно',
  };
  public errorMessage: string = null;
  public favicon: any;
  private new_count: any = 0;
  private show_timeslot: any = 0;

  constructor(private dataService: DataService, public trello: TrelloService) {

    this.subscribeData = dataService.get().subscribe(
        (response: Glonass[]) => {

          this.createTrelloSubscribe();

          response.map((item) => {
            if( this.plates.indexOf(item.plate) === -1 ){
              this.plates.push(item.plate);
              if( this.countStartData ){ this.new_count++; item.new_record = 1; }
              this.data.push(item);
            }else{
              this.data.forEach((auto) => {
                  if(auto.ids === item.ids) {
                    auto.status = item.status;
                    auto.balance = item.balance;
                    auto.deviceId = item.deviceId;
                    auto.relation = item.relation;
                    auto.comment = item.comment;
                    auto.main_comment = item.main_comment;
                  }
              });
            }

          });

          this.countStartData = this.data.length;
          if(this.countStartData === 0) this.errorMessage = 'На терминале нет авто';
          else this.errorMessage = null;

          if( this.show_timeslot === 0 ) {
            this.timeslots();
            this.checkTrello();
            this.show_timeslot = 1;
          }

          if ( this.new_count ) {
            this.favicon.badge( this.new_count );
          }else { this.favicon.reset(); }


        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );


  }

  copy(event: Event, plate, str) {

    this.highlightedDiv = (this.highlightedDiv === plate) ? 0 : plate ;

    const selBox = document.createElement('textarea');

    selBox.style.position = 'fixed';
    selBox.style.left = '0';
    selBox.style.top = '0';
    selBox.style.opacity = '0';
    selBox.value = plate + '' + str;

    document.body.appendChild(selBox);
    selBox.focus();
    selBox.select();

    document.execCommand('copy');
    document.body.removeChild(selBox);
    event.preventDefault();
  }

  createTrelloSubscribe(){
    this.subscribeTrelloList = this.trello.getTrelloListObservable().subscribe((message) => {
      if(message) {
        this.trelloList.push(message);
        this.trelloList[0].forEach( ( item ) => {
          this.data.map( (x) => {
            if( x.plate === item.name.substring(0, item.name.indexOf(',')) ){
               x.trello = 1;
            }
          });

        });
      }
    });
  }

  checkTrello() {
    this.subscribeTrello = this.trello.getTrelloList().subscribe();
  }


  timeslots(): void {
    this.subscribeDataTimeslots = this.dataService.get('glonass.data.timeslots.php', 20 ).subscribe(
        (response: Glonass[]) => {
          this.timeslotsData = response;
        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );
  }

  delete(event: Event, item: Glonass, temp ): void {
    event.preventDefault();
    this.subscribeDelete = this.dataService.delete(item.ids).subscribe( res => {
      if (res.status === 'true') {
        const index = temp.indexOf( item );
        if ( index > -1) {
          temp.splice( index, 1 );
        }
        this.errorMessage = null;
      } else {
        this.errorMessage = 'Error: ' + res.errorMsg;
      }
    },
    error => this.errorMessage = error
    );
  }

  addComment(item: Glonass, text: string): void {
    this.dataService.updateComment(item.plate, text).then( res => {

      if (res.status === 'true') {
        item.comment += ' \n' + text;
        text = '';
      }
    } );
  }

  getBalance(event: Event, item: Glonass) {
    event.preventDefault();
    if ( item.balance === 0 ) return;

    this.item = item;
   // item.new_record = 0;
    this.balanceApp.show(item.plate);

  }

  show(event: Event, item: Glonass) {
    if ( item.new_record === 1) {
      this.new_count--;
      if ( this.new_count < 1 ) this.favicon.reset();
      else this.favicon.badge( this.new_count );
    }
    this.item = item;
    item.new_record = 0;
    this.modalApp.show();
    event.preventDefault();
  }

  addTaskToTrello( param: number) {
    this.trello.addTask(this.item, param);
    this.plates.push( this.item.plate );
    this.item.trello = 1;
    this.modalApp.hide();
  }
  ngOnInit() {
    this.trello.checkAuth();
    this.favicon = new Favico({animation: 'pop'});
  }

  ngOnDestroy() {
    this.subscribeData.unsubscribe();
    this.subscribeDelete.unsubscribe();
    this.subscribeTrello.unsubscribe();
    this.subscribeTrelloList.unsubscribe();
  }
}


