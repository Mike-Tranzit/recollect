import {Component, OnInit, OnDestroy, ViewChild} from '@angular/core';
import 'rxjs/add/operator/takeWhile';
import {DataService, AlertService} from '../../../_services/index';
import {Tasks} from '../../../_class/tasks';
import {Tarif} from '../../../_class/tarif';
import {ActivatedRoute} from '@angular/router';
import {ModalComponent} from '../../../_components/modal/modal.component';
import * as moment from 'moment';
import {Subscription} from 'rxjs/Subscription';

@Component({
    selector: 'app-list-view',
    templateUrl: './list-view.component.html',
    styleUrls: ['./list-view.component.css']
})
export class ListViewComponent implements OnInit, OnDestroy {
    public itemId: number;
    public active: boolean;
    public plate: string;
    private alive: boolean = true;
    public coordinateSubscribe: Subscription;
    public phoneCopy: boolean = false;
    public coordinate_color: string = 'green';
    private buttonTitle: Object[] = [{'title': 'На связи'}, {'title': 'Установил'}];
    public currentId: number;
    public old_sim_number: string;

    public currentYear: string;
    @ViewChild('modal') public modalApp: ModalComponent;
    public data: Tasks;
    public tarifs: any = [];
    public tarifsChecked: any = [];

    constructor(private alertService: AlertService, private ActivatedRoute: ActivatedRoute, private dataService: DataService) {
        const localStorageUser = localStorage.getItem('currentUser');
        this.currentId = JSON.parse(localStorageUser).id;
        this.currentYear = moment().format('YY');
    }

    findIndexToUpdate(i): boolean {
        return i.id === this;
    }

    public checked(i): void {
        const updateIndex = this.tarifsChecked.find(this.findIndexToUpdate, i.id);
        const index = this.tarifsChecked.indexOf(updateIndex);
        if (index !== -1) {
            this.tarifsChecked.splice(index, 1);
        } else {
            this.tarifsChecked.push(i);
        }
    }

    private getTaskById() {
        this.dataService.getTaskById(this.itemId).then((res: Tasks) => {
            this.active = false;
            this.tarifs = res.tarifts;
            this.data = res;
            this.old_sim_number = this.data.sim;
            if (this.coordinateSubscribe) this.coordinateSubscribe.unsubscribe();
            this.getLastCoordinate();
        });
    }

    public addInstallerComment(event: Event): void {
        event.preventDefault();
        this.dataService.updateCommentInstaller(this.data.id, this.data.installer_comment).then(res => {
            if (res.status === 200) {
                this.alertService.success(res.message);
            } else {
                this.alertService.error(res.message);
            }
        });
    }

    public changeStatus(event: Event, status: number): void {
        event.preventDefault();
        this.dataService.updateStatus(status, this.data.id, this.currentId).then(res => {
            if (res.status === 200) {
                this.data.status = status;
                this.data.userGet = this.currentId;
                this.alertService.success(res.message);
            } else {
                this.alertService.error(res.message);
            }
        });
    }

    public addDeviceId(deviceId: string, sim: string): any {
        this.dataService.updateDeviceId(this.data.plate, this.data.id, this.data.glonass_id, deviceId, sim, this.old_sim_number).then(res => {
            if (res.status === 200) {
                this.alertService.success(res.message);
            } else {
                this.alertService.error(res.message);
            }
            this.getTaskById();
        });
    }


    public getLastCoordinate(): void {
        this.coordinateSubscribe = this.dataService.getLastCoordinate(this.data.device_id).takeWhile(() => this.alive).subscribe((res: any) => {
            if (res.last_coordinate !== 'null') {
                this.coordinate_color = ( (Date.parse(new Date().toString()) - Date.parse(res.last_coordinate)) < 60 * 60 * 1000 ) ? 'green' : 'red';
            } else {
                this.coordinate_color = 'red';
            }
            this.data.last_coordinate = res.last_coordinate;
        });
    }

    changeSanction(event: Event, status: number): void {
        event.preventDefault();
        this.dataService.changeSanction(this.data.obj_id, this.data.glonass_id, status).then(res => {
                if (res.status === 200) {
                    this.data.sanction = res.status_sanction;
                    this.data.date_sanction = res.date_sanction;
                }
            }
        );
    }

    public onTouch(): void {
        this.dataService.completeTask(this.data.id, this.tarifsChecked).then(res => {
            if (res.status === 200) {
                this.data.status = 2;
                if (this.data.sanction === 0) this.data.sanction = 1;
                this.alertService.success(res.message);
            } else {
                this.alertService.error(res.message);
            }
            this.modalApp.hide();
        });
    }

    public addAct(act_day: string, act_month: string, act_year: string, act_index: string): void {
        const act = act_day + '/' + act_month + '/' + act_year + '-' + act_index;
        this.dataService.updateAct(this.data.plate, act).then(res => {
            if (res.status === 200) {
                this.data.act = act;
                this.alertService.success(res.message);
            } else {
                this.alertService.error(res.message);
            }
        });
    }

    copy(event: Event) {
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
        this.ActivatedRoute.params.takeWhile(() => this.alive).subscribe(params => {
            this.itemId = params['itemId'];
            this.active = true;
            this.getTaskById();
        });
    }

    ngOnDestroy() {
        this.alive = false;
    }
}