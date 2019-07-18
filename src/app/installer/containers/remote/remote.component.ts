import {Component, OnInit, OnDestroy} from '@angular/core';
import {RemoteService, AlertService} from '../../../_services/index';
import {Phone} from '../../../_class/phone';
import 'rxjs/add/operator/takeWhile';

@Component({
    selector: 'app-remote',
    templateUrl: './remote.component.html',
    styleUrls: ['./remote.component.css']
})
export class RemoteComponent implements OnInit, OnDestroy {
    public remoteData: string;
    public alive = true;
    public title = 'Удаленный доступ';
    public loader = false;
    public resultPhone: Phone = new Phone;

    constructor(private alertService: AlertService, private remoteService: RemoteService) {
    }

    clearData() {
        this.resultPhone.status = null;
        this.resultPhone.text = null;
        this.resultPhone.phone = null;
        this.resultPhone.plate = null;
    }

    checPhoneInTrucks(): void {
        this.remoteService.checPhoneInTrucks(this.remoteData).takeWhile(() => this.alive).subscribe((v: Phone) => {
            this.clearData();
            if (v.status === 200) {
                this.resultPhone.phone = v.phone;
                this.resultPhone.status = v.status;
                this.resultPhone.plate = v.plate;
            }
            if (v.status === 401) this.alertService.error('К данному номеру не приклеплена SIM');
        });
    }

    sendCookieMessage(): void {
        this.loader = true;
        this.remoteService.sendCookieMessage(this.resultPhone).takeWhile(() => this.alive).subscribe((v: any) => {
            this.loader = false;
            if (v.status === 200) {
                this.clearData();
                this.alertService.success('Успешно!');
            }
            if (v.status === 401) this.alertService.error('Ошибка. Попробуйте позже.');
        });
    }

    ngOnDestroy() {
        this.alive = false;
    }

    ngOnInit() {
    }
}