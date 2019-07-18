import {Injectable} from '@angular/core';
import {Http, Headers, Response, RequestOptions} from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/toPromise';
import 'rxjs/add/observable/throw';
import {Glonass} from '../_class/glonass';
import {Observable} from 'rxjs/Rx';
import {environment} from '../../environments/environment';

export interface Linkdata {
    deviceId: string;
    plate?: string;
    last_coordinate: string;
}

@Injectable()
export class DataService {

    private host: string;
    public LinkdataArray: Linkdata[] = [];

    constructor(private http: Http) {
        this.host = environment.host;
    }

    delete(id: number) {
        const body = 'data=' + JSON.stringify({id: id});
        return this.postRequest(body, 'data/deleteFromReminder');
    }

    private extractData(res: Response) {
        const body = res.json();
        return body || {};
    }

    getHeaders(): Headers {
        return new Headers({'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'});
    }

    public addTask(plate: string, ids: number, glonass_id: number, comment: string, param: number): Promise<any> {
        const currUser = localStorage.getItem('currentUser');
        const type = param === 3 ? 1 : 2;
        const body = 'data=' + JSON.stringify({
                plate: plate,
                ids: ids,
                comment: comment,
                type: type,
                glonass_id: glonass_id,
                user: JSON.parse(currUser).id
            });
        return this.postRequest(body, 'data/addTask');
    }

    public updateComment(plate: string, text: string): Promise<any> {
        const body = 'data=' + JSON.stringify({plate: plate, text: text + '.'});
        return this.postRequest(body, 'data/updateComment');
    }

    public updateAct(plate: string, act: string): Promise<any> {
        const body = 'data=' + JSON.stringify({plate: plate, act: act});
        return this.postRequest(body, 'installer/updateAct');
    }

    public updateSim(id: number, sim: string, old_sim_number: string): Promise<any> {
        const body = 'data=' + JSON.stringify({
                id: id,
                sim: sim,
                old_sim_number: old_sim_number
            });
        return this.postRequest(body, 'installer/updateSim');
    }

    public updateStatus(status: number, id: number, user: number): Promise<any> {
        const body = 'data=' + JSON.stringify({status: status, user: user, id: id});
        return this.postRequest(body, 'installer/updateStatus');
    }

    public updateCommentInstaller(id: number, installer_comment: string): Promise<any> {
        const body = 'data=' + JSON.stringify({installer_comment: installer_comment, id: id});
        return this.postRequest(body, 'installer/updateCommentInstaller');
    }

    public completeTask(id: number, tarifsChecked: any = []): Promise<any> {
        const currUser = localStorage.getItem('currentUser');
        const body = 'data=' + JSON.stringify({id: id, user: JSON.parse(currUser).id, tarifsChecked: tarifsChecked});
        return this.postRequest(body, 'installer/completeTask');
    }

    public updateDeviceId(plate: string, id: number, glonass_id: number, deviceId: string, sim: string, old_sim_number: string): Promise<any> {
        const body = 'data=' + JSON.stringify({
                plate: plate,
                id: id,
                glonass_id: glonass_id,
                device_id: deviceId,
                sim: sim,
                old_sim_number: old_sim_number
            });
        return this.postRequest(body, 'installer/updateDeviceId');
    }

    public checkMassCoordinates(arrDeviceId: Object[]): Observable<any> {
        const headers = this.getHeaders();
        const body = 'data=' + JSON.stringify({arrDeviceId: arrDeviceId});
        return this.http.post(`${this.host}installer/checkMassCoordinates`, body, {headers}).map(this.extractData);
    }

    public getLastCoordinate(deviceId: string): Observable<any> {
        const headers = this.getHeaders();
        const body = 'data=' + JSON.stringify({device_id: deviceId});
        return Observable.timer(0, 60000 * 5).switchMap(() => this.http.post(`${this.host}installer/getLastCoordinate`, body, {headers}).map(this.extractData));
    }

    public test(): any {
        return this.http.get('https://randomuser.me/api/').shareReplay();
    }

    public changeSanction(id: number, glonass_id: any, status: number): Promise<any> {
        console.log(glonass_id);
        const currUser = localStorage.getItem('currentUser');
        const body = 'data=' + JSON.stringify({glonass_id: glonass_id, id: id, status: status, user: JSON.parse(currUser).id});
        return this.postRequest(body, 'data/changeSanction');
    }

    public postRequest(body: string, path: string): Promise<any> {
        const headers = this.getHeaders();
        return this.http.post(`${this.host}${path}`, body, {headers}).map(this.extractData).toPromise().catch(this.handleError);
    }

    /*  */
    public getTaskById(id: number): Promise<any> {
        const body = 'data=' + JSON.stringify({id: id});
        return this.postRequest(body, 'installer/getObjectForInstallerById');
    }

    public getTruckById(id: number): Promise<any> {
        const body = 'data=' + JSON.stringify({id: id});
        return this.postRequest(body, 'installer/getTruckForInstallerById');
    }

    public get(path = '', timer = 15): Observable<any> {
        return Observable.timer(0, 60000 * 10)
            .switchMap(() => this.http.get(`${this.host}${path}`).map(data => data.text() ? data.json() : data));
    }

    private handleError(error: any) {
        const errMsg = (error.message) ? error.message :
            error.status ? `${error.status} - ${error.statusText}` : 'Server error';
        console.error(errMsg);
        return Observable.throw(errMsg);
    }
}