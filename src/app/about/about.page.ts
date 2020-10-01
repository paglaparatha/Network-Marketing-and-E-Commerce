import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ConnectApiService } from '../connect-api.service';
import { User } from '../models/user.model';
import { upload } from '../utils';

@Component({
  selector: 'app-about',
  templateUrl: './about.page.html',
  styleUrls: ['./about.page.scss'],
})
export class AboutPage implements OnInit {

  email: string;
  user: User;
  path: string = upload;

  slideOpts = {
    slidesPerView: 1,
    initialSlide: 0,
    speed: 400
  };
  constructor(private activatedRoute: ActivatedRoute, private api: ConnectApiService) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.activatedRoute.paramMap.subscribe(params => {
      params.get('email') ? this.email = params.get('email') : this.email = localStorage.getItem('email')
      this.api.onGetUser(this.email).subscribe(res => {
        this.user = res;
      })
    })
  }

}
