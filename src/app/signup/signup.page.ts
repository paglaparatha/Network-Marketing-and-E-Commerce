import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertController, MenuController } from '@ionic/angular';
import { ConnectApiService } from '../connect-api.service';
import { Company } from '../models/company.model';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.page.html',
  styleUrls: ['./signup.page.scss'],
})
export class SignupPage implements OnInit {

  companies: Company[];
  constructor(private api: ConnectApiService, private router: Router, public alertController: AlertController, public menuCtrl: MenuController) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.menuCtrl.enable(false);
    this.api.onGetCompanies().subscribe(res => {
      this.companies = res;
    })
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

  onSignUp(form: NgForm) {
    let myForm = new FormData();

    for (const key in form.value) {
      let element = form.value[key];
      if (key == 'super_ref' && element == "") {
        element = 'null'
      }
      myForm.append(key, element);
    }

    myForm.append('signup', 'true')

    this.api.onSignUp(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert(res.description)
      } else {
        this.router.navigate(['/login'])
      }
    })

  }

}
