import { Component, OnInit, OnDestroy  } from '@angular/core';
import { DataService } from '../../_services/data.service';
import { Glonass } from '../../_class/glonass';
import { Subscription } from 'rxjs/Subscription';
//import {Popup} from 'ng2-opd-popup';

declare var window: any;

@Component({
  selector: 'app-main',
  templateUrl: 'main.component.html',
  styleUrls: ['main.component.css'],
  providers: [DataService]
})
export class MainComponent implements OnInit, OnDestroy {

  private data: Glonass[] = [];
  private timeslotsData: Glonass[] = [];

  private subscribeData: Subscription;
  private subscribeDataTimeslots: Subscription;
  private subscribeDelete: Subscription;
  public countStartData: number = null;
  public statusTitle: any = {
  '=1': 'На НАТ',
  '=2': 'Ушел с НАТ',
  '=3': 'На НЗТ',
  '=4': 'Разгружен',
  'other': 'Не понятно',
  };
  public errorMessage: string = null;

  authenticationSuccess = function() {
    console.log('Successful authentication');
  };

  authenticationFailure = function() {
    console.log('Failed authentication');
  };

  constructor(private dataService: DataService) {
  //  this.popup.show();
   /* window.Trello.authorize({
      type: 'popup',
      name: 'Getting Started Application',
      scope: {
        read: 'true',
        write: 'true' },
      expiration: 'never',
      success: this.authenticationSuccess,
      error: this.authenticationFailure
    });

    console.log( window.Trello.token() );

    const myList = '5981dcb60eb000738d7afe9c';

    const creationSuccess = function (data) {
      console.log('Card created successfully.');
      console.log(JSON.stringify(data, null, 2));
    };

    const newCard = {
      name: 'New Test Card',
      desc: 'This is the description of our new card.',
      idList: myList,
      pos: 'top'
    };

    window.Trello.post('/cards/', newCard, creationSuccess);*/

    this.subscribeData = dataService.get().subscribe(
        (response: Glonass[]) => {
         // console.log(response);
          this.data = response;
          this.countStartData = this.data.length;
          //this.timeslots();
        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );
  }

  trelloConfirm(item: Glonass) {

  }

  timeslots(): void {
    this.subscribeDataTimeslots = this.dataService.get('glonass.data.timeslots.php').subscribe(
        (response: Glonass[]) => {
         // console.log(response);
          this.timeslotsData = response;
         // this.countStartData = this.data.length;
        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );
  }

  delete(item: Glonass): void {

    this.subscribeDelete = this.dataService.delete(item.ids).subscribe( res => {

      if (res.status === 'true') {
        const index = this.data.indexOf( item );
        if ( index > -1) {
          this.data.splice( index, 1 );
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
    this.dataService.updateComment(item.plate, text).subscribe( res => {

      if (res.status === 'true') {
        item.comment += ' \n' + text;
      }
    } );
  }

  ngOnInit() {

  }

  ngOnDestroy() {
    this.subscribeData.unsubscribe();
    this.subscribeDelete.unsubscribe();
  }
  /*
  * 1 - очистка инпут после добавления комментария
  * 2 - вывод оплат
  * 3 - api trello c confirm переход/не переход.
  * */
}
