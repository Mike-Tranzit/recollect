import { Injectable } from '@angular/core';
import { Glonass } from '../_class/glonass';
import 'rxjs/Rx';
import {Observable, Subject} from 'rxjs/Rx';


declare var window: any;

@Injectable()
export class TrelloService {
  private workList = '59832595fefd0ce419be71a3'; //59832595fefd0ce419be71a3 //584129b0a2f353cff49ec9d0
  public trelloList = new Subject<any>();
  constructor() { }

  getTrelloListObservable(): Observable<any> {
    return this.trelloList.asObservable();
  }
  clearMessage(): void {
    this.trelloList.next();
  }

  private authorize() {
    window.Trello.authorize({
      type: 'popup',
      name: 'Ремаиндер Порт-Транзит',
      scope: {
        read: 'true',
        write: 'true' },
      expiration: 'never',
      success: this.authenticationSuccess,
      error: this.authenticationFailure
    });
  }

  public checkAuth(): void { // if (!window.Trello.token())
   this.authorize();
   /*localStorage.setItem('testString', 'test');
   const retrievedObject = localStorage.getItem('testString');*/
   //console.log(window.Trello.token());
  }

  public addTask(item: Glonass, param) {
     const creationSuccess = function (data){
      console.log('Card created successfully.');
     };
   //  const transition = param === 1 ? 'Переход' : '' ;
    let transition = '';
     switch (param) {
         case 1: { transition = 'Переход'; break; }
         case 3: { transition = 'Вывод на связь'; break; }
     }

     const newCard = {
     name: item.plate + ', ' + item.phone + ' ' + transition,
     desc: '',
     idList: this.workList,
     pos: 'top'
     };
     window.Trello.post('/cards/', newCard, creationSuccess);
  }

  public getTrelloList(): any {
        return Observable.timer(0, 60000 * 10).map(
            () => window.Trello.get('/lists/' + this.workList + '/cards?fields=name', (data) => {
                this.clearMessage();
                this.trelloList.next( data );
        })
        );
  }

  authenticationSuccess = function(){
    console.log('Successful authentication');
  };

  authenticationFailure = function(){
    console.log('Failed authentication');
  };
}