import { Injectable } from '@angular/core';
import { Http, Headers, Response, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/toPromise';
import 'rxjs/add/observable/throw';
import { Glonass } from '../_class/glonass';
import {Observable} from 'rxjs/Rx';
import {environment} from '../../environments/environment';

@Injectable()
export class DataService {

  private host: string;
  private timerDelay: number = 15;
  private coordinateDelay: number = 1;
  constructor(private http: Http) {
      this.host = environment.host;
  }

   delete(id: number, path = 'glonass.data.delete.php') {
       const options = this.formingHeader( {id: id} );
       return this.http.delete(`${this.host}${path}`, options).map(this.extractData).catch(this.handleError);
   }

    private extractData(res: Response) {
        const body = res.json();
        return body || {};
    }

    formingHeader(data: Object): RequestOptions {
        const headers = new Headers({ 'Content-Type': 'application/json' });
        return new RequestOptions({ headers: headers, body: data });
    }

    getHeaders(): Headers {
        return new Headers({'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'});
    }

    public addTask(plate: string, ids: number, glonass_id: number, param: number): Promise<any> {
        const currUser = localStorage.getItem('currentUser');
        const type = param === 3 ? 1 : 2;
        const body = 'data=' + JSON.stringify({plate: plate, ids: ids, type: type, glonass_id: glonass_id, user: JSON.parse(currUser).id});
        return this.postRequest(body, 'data/addTask');
    }

    public updateComment(plate: string, text: string ): Promise<any> {
        const body = 'data=' + JSON.stringify({plate: plate, text: text + '.'});
        return this.postRequest(body, 'data/updateComment');
    }

    public updateAct(plate: string, act: string): Promise<any> {
        const body = 'data=' + JSON.stringify({plate: plate, act: act});
        return this.postRequest(body, 'installer/updateAct');
    }

    public completeTask(id: number): Promise<any> {
        const currUser = localStorage.getItem('currentUser');
        const body = 'data=' + JSON.stringify({id: id, user: JSON.parse(currUser).id});
        return this.postRequest(body, 'installer/completeTask');
    }

    public updateDeviceId(plate: string, id: number, glonass_id: number, deviceId: string): Promise<any> {
        const body = 'data=' + JSON.stringify({plate: plate, id: id,  glonass_id: glonass_id, device_id: deviceId});
        return this.postRequest(body, 'installer/updateDeviceId');
    }

    public getLastCoordinate(deviceId: string): Observable<any> {
        const headers = this.getHeaders();
        const body = 'data=' + JSON.stringify({device_id: deviceId});
        return Observable.timer(0, 60000 * this.coordinateDelay).switchMap( () => this.http.post(`${this.host}/installer/getLastCoordinate`, body , { headers } ).map( this.extractData ));
    }

    public changeSanction(id: number, status: number): Promise<any> {
        const currUser = localStorage.getItem('currentUser');
        const body = 'data=' + JSON.stringify({id: id, status: status, user: JSON.parse(currUser).id});
        return this.postRequest(body, 'data/changeSanction');
    }

    public postRequest(body: string , path: string): Promise<any> {
        const headers = this.getHeaders();
        return this.http.post(`${this.host}${path}`, body , { headers } ).map(this.extractData).toPromise().catch        (this.handleError);
    }

    /*  */
   public getTaskById(id: number ): Promise<any> {
       const body = 'data=' + JSON.stringify({id: id});
       return this.postRequest(body, 'installer/getObjectForInstallerById' );
   }

   public get(path = '', timer = 15): Observable<any> {
      return this.http.get(`${this.host}${path}`).map( data => data.text() ? data.json() : data );
    // return this.http.get(`${this.host}`).map( data => console.log(data) );


      /*return Observable.timer(0, 60000 * this.timerDelay)
          .switchMap( () => this.http.get(`${this.host}${path}`).map( data => data.text() ? data.json() : data ) );*/
   }

    private handleError(error: any) {
        const errMsg = (error.message) ? error.message :
            error.status ? `${error.status} - ${error.statusText}` : 'Server error';
        console.error(errMsg);
        return Observable.throw(errMsg);
    }
}