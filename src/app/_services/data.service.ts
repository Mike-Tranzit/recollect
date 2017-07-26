import { Injectable } from '@angular/core';
import { Http, Response } from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import {Observable} from 'rxjs/Rx';
import {environment} from '../../environments/environment';
import {Glonass} from '../_class/glonass';

@Injectable()
export class DataService {

  private host: string;
  constructor(private http: Http) {
      this.host = environment.host;
  }

  get(path = 'glonass.data.php'): Observable<Glonass[]> {
      return this.http.get(this.host + path).map( data => data.text() ? data.json() : data );
  }
}