import { Component, OnInit } from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {Truck} from '../../../_class/truck';
import {DataService, AlertService} from '../../../_services/index';

@Component({
  selector: 'app-sim-view',
  templateUrl: './sim-view.component.html',
  styleUrls: ['./sim-view.component.css']
})

export class SimViewComponent implements OnInit {

  public itemId: number;
  public active: boolean;
  public plate: string;
  private alive: boolean = true;
  public old_sim_number: string;

  public currentYear: string;
  public data: Truck;

  constructor(private ActivatedRoute: ActivatedRoute, private alertService: AlertService, private dataService: DataService) { }

  private getTruckById() {
    this.dataService.getTruckById(this.itemId).then((res: Truck) => {
      this.active = false;
      this.data = res;
      this.old_sim_number = this.data.sim;
    });
  }

  public addSim(sim: string) {
    this.dataService.updateSim(this.data.id, sim, this.old_sim_number).then(res => {
      if (res.status === 200) {
       this.alertService.success(res.message);
      } else {
        this.alertService.error(res.message);
      }
         this.getTruckById();
    });
  }


  ngOnInit() {
    this.ActivatedRoute.params.takeWhile(() => this.alive).subscribe(params => {
      this.itemId = params['id'];
      this.active = true;
      this.getTruckById();
    });
  }

}
