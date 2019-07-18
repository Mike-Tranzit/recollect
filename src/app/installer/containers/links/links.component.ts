import { Component, OnInit, OnDestroy } from '@angular/core';
import { DataService } from '../../../_services/index';
import 'rxjs/add/operator/takeWhile';
import { Subscription } from 'rxjs/Subscription';

export interface Linkdata {
  deviceId: string;
  plate?: string;
  last_coordinate: string;
}

@Component({
  selector: 'app-links',
  templateUrl: './links.component.html',
  styleUrls: ['./links.component.css']
})


export class LinksComponent implements OnInit, OnDestroy {
  public title = 'Проверка связи';
  public LinkdataArray: Linkdata[] = [];
  public loader = false;
  private alive = true;

  constructor(private dataService: DataService) {
    if(!dataService.LinkdataArray.length){
      for ( let i = 0; i < 9; i++ ) {
        dataService.LinkdataArray.push({deviceId: '', plate: '', last_coordinate: ''});
      }
    }
    this.LinkdataArray = dataService.LinkdataArray;
  }

  checkCoordinates(): void {
    this.loader = true;
    this.dataService.checkMassCoordinates(this.LinkdataArray).takeWhile( () => this.alive ).subscribe( (v) => {
      this.LinkdataArray = v;
      this.dataService.LinkdataArray = v;
      this.loader = false;
    });
  }

  checkColor(res: Linkdata): string {
      return ( (Date.parse( new Date().toString()) - Date.parse( res.last_coordinate )) < 60 * 60 * 1000 ) ? 'green' : 'red' ;
  }

  checkCoordinate(last_coordinate: string): boolean {
    const coordArray: string[] = ['success_new', 'fail_new', 'null', 'not_exist'];
    return coordArray.indexOf(last_coordinate) > -1 ? false : true;
  }

  ngOnDestroy() {
    this.alive = false;
  }

  clearField(i: number): void {
    this.LinkdataArray[i].deviceId = '';
    this.LinkdataArray[i].last_coordinate = '';
    this.LinkdataArray[i].plate = '';
   // this.dataService.LinkdataArray[i] = this.LinkdataArray[i];
  }

  ngOnInit() {
  }

}