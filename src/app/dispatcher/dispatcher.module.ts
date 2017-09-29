import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { GeneralModule } from '../general/general.module';
import { Routes, RouterModule } from '@angular/router';
import { DispatcherComponent } from './containers/dispatcher/dispatcher.component';
import { BalanceComponent } from '../_components/balance/balance.component';

import { DataService, TrelloService, BalanceService } from '../_services/index';
import { AuthGuard } from '../_guards/auth.guard';

export const dispatcherRoutes: Routes = [
  {
    path: 'dispatcher',
    component: DispatcherComponent,
    canActivate: [AuthGuard]
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    GeneralModule,
    RouterModule.forChild(dispatcherRoutes)
  ],
  providers: [ AuthGuard, DataService, TrelloService, BalanceService ],
  declarations: [ DispatcherComponent, BalanceComponent ]
})
export class DispatcherModule { }