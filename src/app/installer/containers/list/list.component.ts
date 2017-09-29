import { Component, OnInit, OnDestroy } from '@angular/core';
import { MenuComponent } from '../menu/menu.component';
import { Router } from '@angular/router';
import { DataService } from '../../../_services/index';
import { Listitem } from '../../../_class/listitem';
import 'rxjs/add/operator/takeWhile';

@Component({
  selector: 'app-list',
  templateUrl: './list.component.html',
  styleUrls: ['./list.component.css']
})
export class ListComponent implements OnInit, OnDestroy {
  public active = true;
  private alive: boolean = true;
  private types: Object[] = [{ 'title' : 'Новая установка', 'color' : 'blue' }, { 'title' : 'Вывод на связь', 'color': 'red' }];

  public list: Listitem[] = [];
  constructor(private dataService: DataService, private router: Router) {
    dataService.get('installer/getTasksList').takeWhile( () => this.alive ).subscribe( (response: Listitem[]) => {
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

  info(event: Event, id: number): void {
      event.preventDefault();
      this.router.navigate(['/list', id ]);
  }
  ngOnInit() {
  }

  ngOnDestroy() {
    this.alive = false;
  }

}
