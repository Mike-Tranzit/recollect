import { Component, OnInit, OnDestroy } from '@angular/core';
import { DataService } from '../../../_services/index';
import { Tasks } from '../../../_class/tasks';
import { Subscription } from 'rxjs/Subscription';

@Component({
  selector: 'app-installer',
  templateUrl: './installer.component.html',
  styleUrls: ['./installer.component.css']
})
export class InstallerComponent implements OnInit, OnDestroy {
  private subscribeData: Subscription;
  private data: Tasks[] = [];
  /*
  *
  *"id" => $info['id'],
   "plate" => $info['plate'],
   "date_create" => $info['date_create'],
   "status" => $info['status'],
   "type" => $info['type'],
   "obj_id" => $info['obj_id'],
   'sanction'=>$status_sanction,
   'date_sanction'=>$date_sanction,
  *
  * */

  constructor( private dataService: DataService ) {
    this.subscribeData = dataService.get('data/getObjectsForInstaller').subscribe(
        (response: Tasks[]) => {
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

  public onTouch(): void {

  }

  public addDeviceId(item: Tasks, deviceId: string): void {
    this.dataService.updateDeviceId(item.plate, item.id, item.glonass_id, deviceId).then( res => {
      if (res.status === 200){
        //item.act =
      }
    } );
  }

  public addAct(item: Tasks, act: string): void {
    this.dataService.updateAct(item.plate, act).then( res => {
      if (res.status === 200){
        item.act = act;
      }
    } );
  }
  ngOnInit() {
  }

  ngOnDestroy() {
    this.subscribeData.unsubscribe();

  }
}