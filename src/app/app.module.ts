import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { Routes, RouterModule } from '@angular/router';

import { AppComponent } from './app.component';
import { MainComponent } from './_components/main/main.component';
import { DataService } from './_services/data.service';

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
    RouterModule.forRoot(routes),
    BrowserModule,
    FormsModule,
    HttpModule
  ],
  providers: [DataService],
  exports: [RouterModule],
  bootstrap: [AppComponent]
})
export class AppModule {
}