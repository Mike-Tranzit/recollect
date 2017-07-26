import { Component, OnInit, OnDestroy  } from '@angular/core';
import {DataService} from '../../_services/data.service';
import {Glonass} from '../../_class/glonass';
import { Subscription } from 'rxjs/Subscription';

@Component({
  selector: 'app-main',
  templateUrl: './main.component.html',
  styleUrls: ['./main.component.css'],
  providers: [DataService]
})
export class MainComponent implements OnInit, OnDestroy {

  private data: Glonass[] = [];
  private subscribeData: Subscription;
  private pageTitle = 'Таймслоты на терминале';
  public statusTitle: any = {
    '=1': 'На терминале',
    '=2': 'Ушел с терминала',
    '=3': 'На территории',
    '=4': 'Разгружен',
    'other': 'Не понятно',
  };

  constructor(private dataService: DataService) {
    this.subscribeData = dataService.get().subscribe(
        (response: Glonass[]) => {
          console.log(response);
          this.data = response;
        },
        (err: any) => {
          console.log('Received error:', err);
        },
        () => {
          console.log('Empty');
        }
    );
  }

  delete(): void {
  }

  ngOnInit() {

  }

  ngOnDestroy() {
    this.subscribeData.unsubscribe();
  }

}