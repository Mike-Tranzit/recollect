import {Component, OnInit, OnDestroy} from '@angular/core';
import {AutosService} from '../../../_services/index';
import {Autos} from '../../../_class/autos';
import 'rxjs/add/operator/takeWhile';
import {Subscription} from 'rxjs/Subscription';

@Component({
    selector: 'app-autos',
    templateUrl: './autos.component.html',
    styleUrls: ['./autos.component.css']
})
export class AutosComponent implements OnInit, OnDestroy {
    private alive = true;
    public title = 'Таймслоты';
    public active = true;
    public plate: string;
    private types: Object[] = [{'title': 'Вывод на связь', 'color': 'red'}, {
        'title': 'Новая установка',
        'color': 'blue'
    }];
    private work: Object[] = [{'title': ''}, {
        'title': ' (в работе)'
    }];
    public autosSubscribe: Subscription;
    public list: Autos[] = [];

    constructor(private AutosService: AutosService) {
        this.loadList();
    }

    loadList(): void {
        if (this.autosSubscribe) this.autosSubscribe.unsubscribe();
        this.autosSubscribe = this.AutosService.get('autos/getWindows').takeWhile(() => this.alive).subscribe((response: Autos[]) => {
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
