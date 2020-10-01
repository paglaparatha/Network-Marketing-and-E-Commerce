import { Component, OnInit } from '@angular/core';
import { upload } from '../utils';
import { ShopServiceService } from './shop-service.service';

@Component({
  selector: 'app-shop',
  templateUrl: './shop.page.html',
  styleUrls: ['./shop.page.scss'],
})
export class ShopPage implements OnInit {
  sliders: {id: number, image: string, loaded: boolean}[];
  categories: {id: number, image: string, loaded: boolean, name: string}[];
  path: string = upload

  slideOpts = {
    speed: 400,
    autoplay: true,
    loop: true
  };
  constructor(private api: ShopServiceService) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.api.onGetSliders().subscribe(res => {
      this.sliders = res;
    });

    this.api.onGetCategories().subscribe(res => {
      this.categories = res;
    })
  }

}
