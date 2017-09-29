import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';
import { GeneralModule } from '../general/general.module';
import { DataService } from '../_services/index';
import { AuthGuard } from '../_guards/auth.guard';
import { ListViewGuard } from '../_guards/list-view.guard';
import { InstallerComponent } from './containers/installer/installer.component';
import { ListComponent } from './containers/list/list.component';
import { MenuComponent } from './containers/menu/menu.component';

import { ListViewComponent } from './containers/list-view/list-view.component';


export const dispatcherRoutes: Routes = [
  {
    path: 'installer',
    component: InstallerComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'list',
    component: ListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'list/:itemId',
    component: ListViewComponent,
    canActivate: [AuthGuard, ListViewGuard]
  }
];

@NgModule({
  imports: [
    GeneralModule,
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    RouterModule.forChild(dispatcherRoutes)
  ],
  providers: [ AuthGuard, DataService, ListViewGuard ],
  declarations: [InstallerComponent, ListComponent, MenuComponent, ListViewComponent]
})
export class InstallerModule { }