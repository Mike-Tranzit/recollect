import { Injectable } from '@angular/core';
import { Http, Headers, Response, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/observable/throw';
import {Observable} from 'rxjs/Rx';
import {environment} from '../../environments/environment';
import {Glonass} from '../_class/glonass';

@Injectable()
export class DataService {

  private host: string;
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

    updateComment(plate: string, text: string, path = 'glonass.data.comment.php' ) {
        const options = this.formingHeader( { plate: plate, text: text + '.' } );
        return this.http.post(`${this.host}${path}`, options).map(this.extractData).catch(this.handleError);
    }

   get(path = 'glonass.data.php'): Observable<Glonass[]> {
      return this.http.get(`${this.host}${path}`).map( data => data.text() ? data.json() : data );
   }

    private handleError(error: any) {
        const errMsg = (error.message) ? error.message :
            error.status ? `${error.status} - ${error.statusText}` : 'Server error';
        console.error(errMsg);
        return Observable.throw(errMsg);
    }
}