import {Component, OnInit, OnDestroy} from '@angular/core';
import {AutosService} from '../../../_services/index';
import {Trucks} from '../../../_class/trucks';
import 'rxjs/add/operator/takeWhile';
import {Subscription} from 'rxjs/Subscription';

@Component({
    selector: 'app-sim',
    templateUrl: './sim.component.html',
    styleUrls: ['./sim.component.css']
})
export class SimComponent implements OnInit, OnDestroy {
    private alive = true;
    public title = 'SIM';
    public active = true;
    public plate: string;
    public autosSubscribe: Subscription;
    public list: Trucks[] = [];

    constructor(private AutosService: AutosService) {
        this.loadList();
    }

    loadList(): void {
        if (this.autosSubscribe) this.autosSubscribe.unsubscribe();
        this.autosSubscribe = this.AutosService.get('installer/getTrucks').takeWhile(() => this.alive).subscribe((response: any) => {
                this.list = response;
                this.active = false;
            },
            (err: any) => {
                console.log('Received error:', err);
                this.active = false;
            },
            () => {
                this.active = false;
                console.log('Empty');
            }
        );
    }

    refreshList(): void {
        this.active = true;
        this.loadList();
    }

    ngOnInit() {
    }

    ngOnDestroy() {
        this.alive = false;
    }
}
