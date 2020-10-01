import { Component } from '@angular/core';
import { ConnectApiService } from '../connect-api.service';
import { User } from '../models/user.model';
import { upload } from '../utils';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
})
export class HomePage {

  user: User;
  path: string = upload

  slideOpts = {
    slidesPerView: 2,
    initialSlide: 0,
    speed: 400
  };
  constructor(private api: ConnectApiService) { }

  ionViewWillEnter() {
    let email = localStorage.getItem('email');
    this.api.onGetUser(email).subscribe(res => {
      this.user = res
    })
  }

  onLogout() {
    this.api.onLogout();
  }
}
