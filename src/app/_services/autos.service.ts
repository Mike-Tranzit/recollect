import {Injectable} from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/toPromise';
import 'rxjs/add/observable/throw';
import {Observable} from 'rxjs/Rx';
import {Autos} from '../_class/autos';
import {Auto} from '../_class/auto';
import {environment} from '../../environments/environment';

@Injectable()
export class AutosService {

    public host: string;

    constructor(private http: Http) {
        this.host = environment.host;
    }

    public changeWindow(windowId, param): Promise<any> {
        const headers = this.getHeaders();
        const currUser = localStorage.getItem('currentUser');
        const body = 'data=' + JSON.stringify({windowId: windowId, param: param, user: JSON.parse(currUser).id});
        return this.http.post(`${this.host}autos/changeWindow`, body, {headers}).map(this.extractData).toPromise().catch(this.handleError);
    }

    public getDataById(windowId: any, glonassId: any): Observable<Auto> {
        const headers = this.getHeaders();
        const body = 'data=' + JSON.stringify({windowId: windowId, glonassId: glonassId});
        return this.http.post(`${this.host}autos/getWindowInfo`, body, {headers}).map(this.extractData).catch(this.handleError);
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

    public get(path = '', timer = 10): Observable<Autos[]> {
        return Observable.timer(0, 60000 * timer)
            .switchMap(() => this.http.get(`${this.host}${path}`).map(data => data.text() ? data.json() : data));
    }
}
