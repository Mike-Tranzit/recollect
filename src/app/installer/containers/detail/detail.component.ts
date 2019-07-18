import {Component, OnInit, OnDestroy, ViewChild} from '@angular/core';
import {AutosService, AlertService, DataService} from '../../../_services/index';
import 'rxjs/add/operator/takeWhile';
import {Auto} from '../../../_class/auto';
import {ModalComponent} from '../../../_components/modal/modal.component';
import {ActivatedRoute} from '@angular/router';

@Component({
    selector: 'app-detail',
    templateUrl: './detail.component.html',
    styleUrls: ['./detail.component.css']
})
export class DetailComponent implements OnInit, OnDestroy {
    public active: boolean;
    public alive = true;
    public data: Auto;
    public addNewTask = true;
    public windowId: number;
    public param: number;
    public type: number;
    public phoneCopy = false;
    public glonassId: number;
    @ViewChild('modal') public modalApp: ModalComponent;

    constructor(private AutosService: AutosService, private alertService: AlertService, private dataService: DataService, private ActivatedRoute: ActivatedRoute) {

    }

    showTask(event: Event): void {
        this.addNewTask = true;
        this.show(event);
    }

    buttonWindow(event: Event, param): void {
        this.addNewTask = false;
        this.param = param;
        this.show(event);
    }

    show(event: Event) {
        this.modalApp.show();
        event.preventDefault();
    }

    changeWindow(): void {
        this.AutosService.changeWindow(this.windowId, this.param).then(res => {
            if (res.status === 401) {
                this.alertService.error(res.message);
            } else {
                this.alertService.success(res.message);
                this.getDataById();
            }
            this.modalApp.hide();
        });
    }

    add(): void {
        const param = (this.type === 0) ? 1 : 2;
        this.dataService.addTask(this.data.plate, 0, this.glonassId, 'Создана установщиком', param).then(res => {
            if (res.status === 401) {
                this.alertService.error(res.message);
            } else {
                this.alertService.success(res.message);
                this.getDataById();
                this.modalApp.hide();
            }
        });
    }

    copy(event: Event): void {
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

    getDataById(): void {
        this.AutosService.getDataById(this.windowId, this.glonassId).takeWhile(() => this.alive).subscribe((res: Auto) => {
            this.active = false;
            this.data = res;
        });
    }

    ngOnInit(): void {
        this.ActivatedRoute.params.takeWhile(() => this.alive).subscribe(params => {
            this.windowId = params['windowId'];
            this.glonassId = params['glonassId'];
            this.type = params['type'];
            this.active = true;
            this.getDataById();
        });
    }

    ngOnDestroy() {
        this.alive = false;
    }
}
