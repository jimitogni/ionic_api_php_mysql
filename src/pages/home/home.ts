import { NavController, AlertController } from 'ionic-angular';
import { Component, ViewChild  } from '@angular/core';
import { Http, Headers, RequestOptions }  from "@angular/http";
import { LoadingController } from 'ionic-angular';

import 'rxjs/add/operator/map';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html',
  providers: [
    ServiceProvider
  ]
})
export class HomePage {

@ViewChild("username") username;
@ViewChild("password") password;

data:string;

  constructor(public navCtrl: NavController,
              public alertCtrl: AlertController,
              private http: Http,
              public loading: LoadingController) {}


//método para login
logIN(){
  //verifica se o campo login nao esta vazio
    if(this.username.value=="" ){
      let alert = this.alertCtrl.create({
      title:"Atenção",
      subTitle:"Nome de usuário não pode ser vazio",
      buttons: ['OK']
      });
      alert.present();
    } else

    //verifica se o campo senha nao esta vazio
    if(this.password.value==""){
      let alert = this.alertCtrl.create({
      title:"Atenção",
      subTitle:"Password não pode ser vazio",
      buttons: ['OK']
      });
      alert.present();
    }
      else{
      //insere os cabeçalhos html para que o servidor reconheça como API
      var headers = new Headers();
      headers.append("Accept", 'application/json');
      headers.append('Content-Type', 'application/json' );

      let options = new RequestOptions({ headers: headers });

      let data = {
      username: this.username.value,
      password: this.password.value
      };

      let loader = this.loading.create({
      content: 'Processando o login...',
      });

      loader.present().then(() => {
      this.http.post('http://jimi.kozow.com:1234/crud/login.php',data,options)
      .map(res => res.json())

      .subscribe(res => {
      console.log(res)
      loader.dismiss()

      if(res=="1"){

        let alert = this.alertCtrl.create({
        title:"Login efetuado com sucesso",
        subTitle:"Redirecione a pagina aqui",
        buttons: ['Entrar']
        });
        alert.present();
        //aqui vc coloca para abrir a página que quiser mostrar para o usuario logado
        //exemplo this.navCtrl.push(Pagina);

      }else{

        let alert = this.alertCtrl.create({
        title:"Erro",
        subTitle:"Usuário ou senha inválidos",
        buttons: ['OK']
        });
        alert.present();
        }

      });
      });
      }
  }


}
