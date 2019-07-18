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

  getHeaders(): Headers {
      return new Headers({'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'});
  }

  login(login: string, password: string): Observable<any> {
    const headers = this.getHeaders();
    const body = 'data=' + JSON.stringify({ login: login, password: password });
  return this.http.post(`${this.host}login/authentication`, body , { headers } )
        .map((response: Response) => {
            const user = response.json();
            if(user.status === 401) throw new Error('РќРµ РІРµСЂРЅС‹Р№ Р»РѕРіРёРЅ РёР»Рё РїР°СЂРѕР»СЊ');
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