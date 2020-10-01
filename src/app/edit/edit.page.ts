import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { AlertController } from '@ionic/angular';
import { ConnectApiService } from '../connect-api.service';
import { User } from '../models/user.model';
import { upload } from '../utils';

@Component({
  selector: 'app-edit',
  templateUrl: './edit.page.html',
  styleUrls: ['./edit.page.scss'],
})
export class EditPage implements OnInit {

  user: User;
  path: string = upload;
  fileToUpload: File;
  npass1 = '';
  npass2 = '';
  constructor(private api: ConnectApiService, public alertController: AlertController) { }

  ngOnInit() {
  }

  async presentAlert(head:string, msg: string) {
    const alert = await this.alertController.create({
      header: head,
      message: msg,
      buttons: ['OK']
    });

    await alert.present();
  }

  ionViewWillEnter() {
    let email = localStorage.getItem('email')
    this.onGetUser(email)
    
  }

  onGetUser(email) {
    this.api.onGetUser(email).subscribe(res => {
      this.user = res;
    })
  }

  onEditProfile(form: NgForm) {
    let myForm = new FormData()

    for (const key in form.value) {
      if (Object.prototype.hasOwnProperty.call(form.value, key)) {
        const element = form.value[key];
        myForm.append(key, element)
      }
    }
    myForm.append('id', this.user.id.toString())
    myForm.append('edit-profile', 'true')

    this.api.onEditUser(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert('Error', res.description)
      } else {
        localStorage.setItem('email', res.description);
        let email = res.description
        this.onGetUser(email)
        this.presentAlert('Success', 'Profile Successfully Updated!')
      }
    })
  }

  handleFileInput(files: FileList) {
    this.fileToUpload = files.item(0);
  }

  onEditImage(form: NgForm) {
    let myForm = new FormData()

    myForm.append('id', this.user.id.toString())
    myForm.append('file', this.fileToUpload, this.fileToUpload.name);
    myForm.append('edit-image', 'true')

    this.api.onEditUser(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert('Error', res.description)
      } else {
        let email = localStorage.getItem('email')
        this.onGetUser(email)
        this.presentAlert('Success', 'Profile Successfully Updated!')
      }
    })
    form.reset()
  }

  onVerifyPasswords() {
    return this.npass1 === this.npass2
  }

  onChangePassword(form: NgForm) {
    let myForm = new FormData()

    for (const key in form.value) {
      if (Object.prototype.hasOwnProperty.call(form.value, key)) {
        const element = form.value[key];
        myForm.append(key, element)
      }
    }

    myForm.append('id', this.user.id.toString())
    myForm.append('change-password', 'true')

    this.api.onChangePassword(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert('Error', res.description)
      } else {
        this.presentAlert('Success', 'Password Successfully Updated!')
      }
    })

    form.reset()
  }

}
