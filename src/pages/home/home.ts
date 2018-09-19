import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';

import { ServiceProvider } from '../../providers/service/service';


@Component({
  selector: 'page-home',
  templateUrl: 'home.html',
  providers: [
    ServiceProvider
  ]
})
export class HomePage {

  constructor(public navCtrl: NavController, public service: ServiceProvider) {}

  titulo : any[];
  sala : any[];
  responseTxt : any;
  public listaResultados = new Array<any>();

    listarTrabalhos(){
      let url = "http://jimi.kozow.com:1234/crud/read.php";

      let serverResponse : Promise<any>;

      serverResponse = this.service.callServer(url);

      serverResponse.then(data => {
        console.log("Recebido " + JSON.stringify(data));
        this.parseJson(data);
      }).catch(err => {
        console.log("erro eh " + err);
      })
    }

  parseJson(data){
    let jsonArray = data;

    this.titulo = [];
    this.sala = [];

    for (let i=0; i < jsonArray.length; i++){
      let jsonObject = jsonArray[i];
      this.titulo.push(jsonObject.titulo);
      this.sala.push(jsonObject.sala);
    }
  }

  showTable(){
      this.service.readTable().subscribe(data =>
                {
                  let response = (data as any);
                  let retorno = JSON.stringify(response);
                  this.responseTxt = retorno;

                  console.log(retorno);
                }, error => {
                  console.log(error);
                }
      );
    }

    showTable2(){
      this.service.readTable().toPromise().then(data =>
                {
                  this.responseTxt = "" + JSON.stringify(data);

                }
      );
    }

    addTable(c, n, o){
      this.service.escreveTabela(c, n, o).then(data =>
                {
                console.log("Eu recebi: " + JSON.stringify(data));
                this.responseTxt = "" + JSON.stringify(data);

                }
      );
    }

}
