import { Component, OnInit, OnDestroy  } from '@angular/core';
import { DataService } from '../../_services/data.service';
import { Glonass } from '../../_class/glonass';
import { Subscription } from 'rxjs/Subscription';

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

  constructor(private dataService: DataService) {
    this.subscribeData = dataService.get().subscribe(
        (response: Glonass[]) => {
         // console.log(response);
          this.data = response;
          this.countStartData = this.data.length;
          this.timeslots();
        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );
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

}
