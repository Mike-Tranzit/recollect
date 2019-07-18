import { Injectable } from '@angular/core';
import { Http, Response, RequestOptions, URLSearchParams } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/observable/throw';
import {Observable} from 'rxjs/Rx';
import {environment} from '../../environments/environment';
import { Balance } from '../_class/balance';

@Injectable()
export class BalanceService {

  private host: string;
  constructor(private http: Http) {
    this.host = environment.host;
  }

  getBalance(plate): Promise<Balance[]> {
    const params: URLSearchParams = new URLSearchParams();
    params.set('plate', plate);

    const requestOptions = new RequestOptions();
    requestOptions.search = params;

     return this.http.get(`${this.host}balance/getPayments`, requestOptions ).toPromise()
          .then( (response: Response) => response.json() as Balance[]).catch(this.handleError);
  }

  private handleError(err) {
    console.log(err);
    return Observable.throw(err || 'Server error');
  }
}
