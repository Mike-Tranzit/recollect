import { Injectable } from '@angular/core';
import { Http, Response, RequestOptions, Headers } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/map';
import {environment} from '../../environments/environment';

@Injectable()
export class AuthenticationService {

  private host: string;
  constructor(private http: Http) {
    this.host = environment.host;
  }

  /*  formingHeader(data: Object): RequestOptions {
        const headers = new Headers({ 'Content-Type': 'application/json' });
        return new RequestOptions({ headers: headers, body: data });
    }*/

  getHeaders(): Headers {
      return new Headers({'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'});
  }

  login(login: string, password: string): Observable<any> {
    const headers = this.getHeaders();
    const body = 'data=' + JSON.stringify({ login: login, password: password });
  return this.http.post(`${this.host}login/authentication`, body , { headers } )
        .map((response: Response) => {
            const user = response.json();
            if(user.status === 401) throw new Error('Не верный логин или пароль');
            if(user.currUser && user.currUser.token) {
                localStorage.setItem('currentUser', JSON.stringify(user.currUser));
                return user.currUser;
            }
        });
  }

  logout(): void {
    localStorage.removeItem('currentUser');
  }
}