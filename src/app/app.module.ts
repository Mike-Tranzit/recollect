import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { Routes, RouterModule } from '@angular/router';

import { AlertService } from './_services/alert.service';
import { AuthenticationService } from './_services/authentication.service';

import { DispatcherModule } from './dispatcher/dispatcher.module';
import { InstallerModule } from './installer/installer.module';

import { LoginComponent } from './login/login.component';
import { AlertComponent } from './alert/alert.component';
import { AppComponent } from './app.component';
import { DateCoordinatePipe } from './_pipes/date-coordinate.pipe';
import { CommonComponent } from './_components/common/common.component';

const routes: Routes = [
  {path: '', redirectTo: '/login', pathMatch: 'full'},
  {path: 'login', component: LoginComponent},
  {path: '**', component: LoginComponent }
];

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    AlertComponent,
    DateCoordinatePipe,
    CommonComponent
  ],
  imports: [
    BrowserModule,
    DispatcherModule,
    InstallerModule,
    FormsModule,
    ReactiveFormsModule,
    HttpModule,
    RouterModule.forRoot(routes)
  ],
  providers: [AuthenticationService, AlertService],
  exports: [RouterModule],
  bootstrap: [AppComponent]
})
export class AppModule {

}