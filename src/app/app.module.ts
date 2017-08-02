import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { Routes, RouterModule } from '@angular/router';

import { AppComponent } from './app.component';
import { MainComponent } from './_components/main/main.component';
import { DataService } from './_services/data.service';
import { PopupModule } from 'ng2-opd-popup';
import { ModalModule } from 'angular2-modal';

const routes: Routes = [
 /* {
    path: '',
    component: AppComponent,
    children: []
  }*/
];

@NgModule({
  declarations: [
    AppComponent,
    MainComponent
  ],
  imports: [
    BrowserModule,
    ModalModule.forRoot(),
    FormsModule,
    HttpModule,
    RouterModule.forRoot(routes)
   // PopupModule.forRoot()
  ],
  providers: [DataService],
  exports: [RouterModule],
  bootstrap: [AppComponent]
})
export class AppModule {

}