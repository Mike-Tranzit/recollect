import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';
import { GeneralModule } from '../general/general.module';
import { DataService, RemoteService, AutosService } from '../_services/index';

import { AuthGuard } from '../_guards/auth.guard';
import { ListViewGuard } from '../_guards/list-view.guard';
import { ListComponent } from './containers/list/list.component';
import { MenuComponent } from './containers/menu/menu.component';

import { ListViewComponent } from './containers/list-view/list-view.component';
import { LinksComponent } from './containers/links/links.component';
import { RemoteComponent } from './containers/remote/remote.component';
import { AutosComponent } from './containers/autos/autos.component';
import { SimComponent } from './containers/sim/sim.component';
import { DetailComponent } from './containers/detail/detail.component';
import { SearchPlatePipe } from '../_pipes/search-plate.pipe';
import { SimViewComponent } from './containers/sim-view/sim-view.component';

export const installerRoutes: Routes = [
  {
    path: 'list',
    component: ListComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'autos',
    component: AutosComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'sim',
    component: SimComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'sim/:id',
    component: SimViewComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'remote',
    component: RemoteComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'detail/:windowId/:glonassId/:type',
    component: DetailComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'links',
    component: LinksComponent,
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
    RouterModule.forChild(installerRoutes)
  ],
  providers: [ RemoteService, AuthGuard, AutosService, DataService, ListViewGuard ],
  declarations: [ListComponent, SimViewComponent, MenuComponent, ListViewComponent, SimComponent, LinksComponent, SearchPlatePipe, RemoteComponent, AutosComponent, DetailComponent, SimViewComponent]
})
export class InstallerModule { }