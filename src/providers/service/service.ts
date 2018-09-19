import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/toPromise'
import 'rxjs/add/observable/from';
import 'rxjs/Rx';


@Injectable()
export class ServiceProvider {

  constructor(public http: HttpClient) {
    console.log('Hello ServiceProvider Provider');
  }

  readTable(){
    return this.http.get("http://jimi.kozow.com:1234/crud/read.php");
  }

  /*readTable2() : Promise<any>{
    let request = this.http.get("http://jimi.kozow.com:1234/crud/read.php");
    return request.toPromise();
  }*/

  escreveTabela(c, n, o) : Promise<any>{
    let url = "http://jimi.kozow.com:1234/crud/escreve.php";

    let param = {codigo : c, nota : n, obs : o};

    let request = this.http.post(url, param);

    return request.toPromise();
  }

  callServer(url) : Promise<any>{
    let response : Promise<any>;

    response = this.http.get(url).toPromise().then(responseData => responseData).catch(err=>this.errorDisplay(err));

    return response;
  }

  errorDisplay(error:any):Promise<any>{
    return Promise.reject(error.message || error);
  }


}
