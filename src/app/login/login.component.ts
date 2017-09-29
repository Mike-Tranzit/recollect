import { Component, OnInit } from '@angular/core';
import { FormGroup, Validators, FormBuilder } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertService, AuthenticationService } from '../_services/index';

@Component({
  selector: 'app-login',
  moduleId: module.id,
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [FormBuilder]
})

export class LoginComponent implements OnInit {

  model: any = {};
  returnUrl: string;

  loginForm: FormGroup;
  constructor(
      private formBuilder: FormBuilder,
      private authenticationService: AuthenticationService,
      private router: Router,
      private alertService: AlertService
  ){
    this.loginForm = formBuilder.group({
      'login': ['+79181111105', [Validators.required]],
      'password': ['1111', [ Validators.required]]
    });
  }

  ngOnInit() {
    this.authenticationService.logout();
  }

  login(): any {

    if (this.loginForm.dirty && this.loginForm.valid) {
      this.authenticationService.login(this.loginForm.value.login, this.loginForm.value.password)
          .subscribe(
              data => {
                const redirectUrl = data.role === 1 ? '/dispatcher' : '/list' ;
                this.router.navigate([redirectUrl]);
              },
              error => {
                this.alertService.error(error);
              });
    }

  }

}
