import {Component, OnInit, OnDestroy} from '@angular/core';
import {MenuComponent} from '../menu/menu.component';
import {DataService} from '../../../_services/index';
import {Listitem} from '../../../_class/listitem';
import 'rxjs/add/operator/takeWhile';
import {Subscription} from 'rxjs/Subscription';

@Component({
    selector: 'app-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.css']
})
export class ListComponent implements OnInit, OnDestroy {
    public active = true;
    private alive = true;
    public currentId: number;
    public title = 'Задачи';
    public coordinateSubscribe: Subscription;
    private types: Object[] = [{'title': 'Вывод на связь', 'color': 'red'}, {
        'title': 'Новая установка',
        'color': 'blue'
    }];
    private status: Object[] = [{'title': 'На терминале', 'color': 'green'}, {
        'title': 'Ушел с терминала',
        'color': '#FF0000'
    }, {'title': 'На территории стивидора', 'color': '#CF290E'}, {'title': 'Разгружен', 'color': '#9C1E09'}];

    public list: Listitem[] = [];

    constructor(private dataService: DataService) {
        const localStorageUser = localStorage.getItem('currentUser');
        this.currentId = JSON.parse(localStorageUser).id;
        this.loadList();
    }

    loadList(): void {
        if (this.coordinateSubscribe) this.coordinateSubscribe.unsubscribe();
        this.coordinateSubscribe = this.dataService.get('installer/getTasksList').takeWhile(() => this.alive).subscribe((response: Listitem[]) => {
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

    ngOnInit() {
    }

    refreshList(): void {
        this.active = true;
        this.loadList();
    }

    ngOnDestroy() {
        this.alive = false;
    }

}
