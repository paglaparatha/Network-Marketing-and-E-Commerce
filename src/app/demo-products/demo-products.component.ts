import { Component, OnInit } from '@angular/core';
import { ConnectApiService } from '../connect-api.service';
import { Product } from '../models/product.model';
import { User } from '../models/user.model';
import { ShopServiceService } from '../shop/shop-service.service';
import { upload } from '../utils';

@Component({
  selector: 'app-demo-products',
  templateUrl: './demo-products.component.html',
  styleUrls: ['./demo-products.component.scss'],
})
export class DemoProductsComponent implements OnInit {

  products: Product[]
  path: string = upload
  slideOpts = {
    slidesPerView: 2,
    initialSlide: 0,
    speed: 400,
    autoplay: true,
    loop: true,
    autoplayDisableOnInteraction: true
  };
  user: User;
  constructor(private api: ShopServiceService, private homeApi: ConnectApiService) { }

  ngOnInit() {
    let email = localStorage.getItem('email');
      this.homeApi.onGetUser(email).subscribe(res => {
        this.user = res
        this.api.onGetDemoProducts(this.user.company).subscribe(res => {
          this.products = res;
        })
      })
  }

}
