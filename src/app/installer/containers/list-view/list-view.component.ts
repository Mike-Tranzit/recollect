import { Component, OnInit, OnDestroy, ViewChild } from '@angular/core';
import 'rxjs/add/operator/takeWhile';
import { DataService, AlertService } from '../../../_services/index';
import { Tasks } from '../../../_class/tasks';
import { ActivatedRoute } from '@angular/router';
import { Router } from '@angular/router';
import { ModalComponent } from '../../../_components/modal/modal.component';

@Component({
  selector: 'app-list-view',
  templateUrl: './list-view.component.html',
  styleUrls: ['./list-view.component.css']
})
export class ListViewComponent implements OnInit, OnDestroy {
  public itemId: number;
  public active: boolean = true;
  private alive: boolean = true;
  public phoneCopy: boolean = false;
  public coordinate_color: string = 'green';
  private buttonTitle: Object[] = [{ 'title' : 'Установил' }, { 'title' : 'На связи' }];

  @ViewChild('modal') public modalApp: ModalComponent;
  public data: Tasks;

  constructor(private router: Router, private alertService: AlertService, private ActivatedRoute: ActivatedRoute, private dataService: DataService) {
  }

  private getTaskById() {
    this.dataService.getTaskById( this.itemId ).then( (res: Tasks) => {
        this.active = !this.active;
        this.data = res;
        this.getLastCoordinate();
    });
  }

  public addDeviceId(deviceId: string): void {
    this.dataService.updateDeviceId(this.data.plate, this.data.id, this.data.glonass_id, deviceId).then( res => {
      if (res.status === 200) {
        this.alertService.success( res.message );
      }else {
        this.alertService.error( res.message );
      }
    } );
  }

  public back(): void {
    this.router.navigate(['/list']);
  }

  public getLastCoordinate(): void {
    this.dataService.getLastCoordinate(this.data.device_id).takeWhile( () => this.alive ).subscribe((res: any) => {
        if ( res.last_coordinate !== 'null' ) {
        this.coordinate_color = ( (Date.parse( new Date().toString()) - Date.parse( res.last_coordinate )) < 60 * 60 * 1000 ) ? 'green' : 'red' ;
        } else { this.coordinate_color = 'red'; }
        this.data.last_coordinate = res.last_coordinate;

    });
  }

  changeSanction( event: Event, status: number ): void {
    event.preventDefault();
    this.dataService.changeSanction(this.data.obj_id, status ).then( res => {
          if (res.status === 200) {
            this.data.sanction = res.status_sanction;
            this.data.date_sanction = res.date_sanction;
          }
        }
    );
  }

  public onTouch(): void {
    this.dataService.completeTask(this.data.id).then( res => {
      if (res.status === 200) {
        this.data.status = 1;
        this.alertService.success( res.message );
      }else {
        this.alertService.error( res.message );
      }
      this.modalApp.hide();
    } );
  }

  public addAct(act: string): void {
    this.dataService.updateAct(this.data.plate, act).then( res => {
      if (res.status === 200) {
        this.data.act = act;
        this.alertService.success( res.message );
      }else {
        this.alertService.error( res.message );
      }
    } );
  }

  copy(event: Event){
    const selBox = document.createElement('input');

    selBox.style.position = 'fixed';
    selBox.style.left = '0';
    selBox.style.top = '0';
    selBox.style.opacity = '0';
    selBox.value = '+7' + this.data.phone;

    document.body.appendChild(selBox);
    selBox.focus();
    selBox.select();

    document.execCommand('copy');
    document.body.removeChild(selBox);
    this.phoneCopy = true;
    event.preventDefault();
  }

  show(event: Event) {
    this.modalApp.show();
    event.preventDefault();
  }

  ngOnInit() {
    this.ActivatedRoute.params.takeWhile( () => this.alive ).subscribe( params => {
      this.itemId = params['itemId'];
      this.getTaskById();
    });
  }

  ngOnDestroy() {
    this.alive = false;
  }

}