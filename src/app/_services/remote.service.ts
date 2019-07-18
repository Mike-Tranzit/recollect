import {Injectable} from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/toPromise';
import 'rxjs/add/observable/throw';
import {Observable} from 'rxjs/Rx';
import {Phone} from '../_class/phone';
import {environment} from '../../environments/environment';


@Injectable()
export class RemoteService {

    private host: string;

    constructor(private http: Http) {
        this.host = environment.host;
    }

    getHeaders(): Headers {
        return new Headers({'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'});
    }

    private extractData(res: Response) {
        const body = res.json();
        return body || {};
    }

    private handleError(error: any) {
        const errMsg = (error.message) ? error.message :
            error.status ? `${error.status} - ${error.statusText}` : 'Server error';
        console.error(errMsg);
        return Observable.throw(errMsg);
    }


    public sendCookieMessage(data: Phone): Observable<any> {
        const headers = this.getHeaders();
        const body = 'data=' + JSON.stringify({param: data});
        return this.http.post(`${this.host}remote/sendCookieMessage`, body, {headers}).map(this.extractData).catch(this.handleError);
    }

    public checPhoneInTrucks(data: string): Observable<any> {
        const headers = this.getHeaders();
        const body = 'data=' + JSON.stringify({param: data});
        return this.http.post(`${this.host}remote/checPhoneInTrucks`, body, {headers}).map(this.extractData).catch(this.handleError);
    }
}