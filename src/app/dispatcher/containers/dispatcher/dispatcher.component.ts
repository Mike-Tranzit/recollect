import { Component, OnInit, OnDestroy, ViewChild, ViewContainerRef, TemplateRef } from '@angular/core';
import { DataService, TrelloService } from '../../../_services/index';
import { Glonass } from '../../../_class/glonass';
import { Subscription } from 'rxjs/Subscription';
import { Router } from '@angular/router';
import "rxjs/add/operator/takeWhile";
import { ModalComponent } from '../../../_components/modal/modal.component';
import { BalanceComponent } from '../../../_components/balance/balance.component';
import * as _ from 'lodash';


declare var Favico: any;

export class addParams {
  phone: string;
  plate: string;
  comment: string;
}

@Component({
  selector: 'app-dispatcher',
  templateUrl: './dispatcher.component.html',
  styleUrls: ['./dispatcher.component.css'],
  providers: [DataService]
})
export class DispatcherComponent implements OnInit, OnDestroy {

  public data: Glonass[] = [];
  private alive: boolean = true;

  public addWithoutId: boolean = false;
  public plates: Array<string> = [];
  private newIds: Array<number> = [];
  public item: Glonass = null;
  public trelloList: any = [];
  public highlightedDiv: number;
  public timeslotsData: Glonass[] = [];

  @ViewChild('modal') public modalApp: ModalComponent;
  @ViewChild('balance') public balanceApp: BalanceComponent;
  public addParams: addParams = ({phone:'',plate:''} as addParams);

  public countStartData: any = null;
  public errorMessage: string = null;
  public successMessage: string = null;
  public favicon: any;
  private new_count: any = 0;
  public show_timeslot: any = 0;
  private status_params: Object[] = [{ 'title' : '','color':'green' },{ 'title' : 'Задача поставлена', 'color':'green' }, { 'title' : 'Взята в работу','color':'#550AFF' },  { 'title' : 'Выполнена','color':'#550AFF' }];
  constructor(private dataService: DataService, private router: Router, public trello: TrelloService){

    dataService.get('data/getObjectsForDistechers').takeWhile(() => this.alive).subscribe(
        (response: Glonass[]) => {

          this.createTrelloSubscribe();
          this.data = response;
          this.new_count = 0;

          _.forEach(this.data, (auto) => {
            if( !_.includes(this.plates, auto.plate) || _.includes(this.newIds, auto.ids)){
              if( this.countStartData ){
                auto.new_record = 1;
                this.newIds.push(auto.ids);
              }
            }
            if( auto.new_record === 1) this.new_count++;
          });

          this.plates = [];
          _.map(response, (item) => {
            this.plates.push(item.plate);
          });

          this.countStartData = this.data.length;
          if(this.countStartData === 0) this.errorMessage = 'На терминале нет авто';
          else this.errorMessage = null;

          if( this.show_timeslot === 0 ) {
            this.timeslots();
            this.checkTrello();
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
    this.trello.getTrelloListObservable().takeWhile(() => this.alive).subscribe((message) => {
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
    this.trello.getTrelloList().takeWhile(() => this.alive).subscribe();
  }


  timeslots(): void {

    this.dataService.get( 'timeslots/getTimeslots', 20 ).takeWhile(() => this.alive).subscribe(
        (response: Glonass[]) => {
          this.timeslotsData = response;
          this.show_timeslot = 1;
        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );
  }

  changeSanction( event: Event, item: Glonass, status: number ): void {
    event.preventDefault();
    this.dataService.changeSanction(item.ids,item.glonass_id,status).then( res => {
          if (res.status === 200){
            item.sanction = res.status_sanction;
            item.date_sanction = res.date_sanction;
            this.errorMessage = null;
          }
        },
        error => this.errorMessage = error
    );
  }

  delete(event: Event, item: Glonass, temp ): void {
   event.preventDefault();
   this.dataService.delete(item.ids).then( res => {
          if (res.status === 200) {
            const index = temp.indexOf( item );
            if ( index > -1) {
              temp.splice( index, 1 );
            }
            this.errorMessage = null;
          }
        },
        error => this.errorMessage = error
    );
  }

  addComment(item: Glonass, text: string): void {
    this.dataService.updateComment(item.plate, text).then( res => {
      if (res.status === 200){
        item.comment += ' \n' + text;
        text = '';
      }
    } );
  }

  getBalance(event: Event, item: Glonass) {
    event.preventDefault();
    if ( item.balance == 0 ) return;

    this.item = item;
    // item.new_record = 0;
    this.balanceApp.show(item.plate);

  }

  show(event: Event, item: Glonass) {
    this.addWithoutId = false;
    if ( item.new_record === 1) {
      this.new_count--;
      if ( this.new_count < 1 ) this.favicon.reset();
      else this.favicon.badge( this.new_count );
    }
    this.newIds.map((i:number) => {
      if(i === item.ids)  this.newIds.splice(this.newIds.indexOf(i),1 );
    });
    this.item = item;
    item.new_record = 0;
    this.modalApp.show();
    event.preventDefault();
  }
  add(event: Event){
    this.addWithoutId = true;
    this.modalApp.show();
    event.preventDefault();
  }

  addTaskToTrello( param: number ): void {
    if( this.addWithoutId ){
      this.item = ({
        ids: 0,
        date: '',
        main_comment: '',
        plate: this.addParams.plate,
        phone: this.addParams.phone,
        comment: "",
        trello: 0,
        status: 0,
        glonass_id: 0
      } as Glonass);
    }
    //console.log(this.item );
    this.dataService.addTask(this.item.plate, this.item.ids, this.item.glonass_id, this.addParams.comment, param).then( res => {
      if (res.status === 401){
        this.successMessage = null;
        this.errorMessage = res.message;
      }else{
        this.errorMessage = null;
        this.successMessage = res.message;
        this.trello.addTask(this.item, this.addParams.comment, param);
        this.addParams = new addParams();
      }
    } );
    this.plates.push( this.item.plate );
    this.item.trello = 1;
    this.item.status = 1;

    this.modalApp.hide();
  }

  ngOnInit() {
    this.trello.checkAuth();
    this.favicon = new Favico({animation: 'pop'});
  }

  ngOnDestroy(){
    this.alive = false;
  }
}