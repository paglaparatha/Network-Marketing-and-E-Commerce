import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertController, MenuController } from '@ionic/angular';
import { ConnectApiService } from '../connect-api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {

  constructor(private api: ConnectApiService, private router: Router, public alertController: AlertController, public menuCtrl: MenuController) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.menuCtrl.enable(false);
  }

  ionViewWillLeave() {
    this.menuCtrl.enable(true);
  }

  async presentAlert(msg: string) {
    const alert = await this.alertController.create({
      header: 'Error',
      message: msg,
      buttons: ['OK']
    });

    await alert.present();
  }

  onLogin(form: NgForm) {
    let myForm = new FormData();

    for (const key in form.value) {
      let element = form.value[key];
      myForm.append(key, element);
    }

    myForm.append('login', 'true')

    this.api.onSignUp(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert(res.description)
      } else {
        localStorage.setItem('email', res.description);
        this.router.navigate(['/home'])
      }
    })
  }

}
