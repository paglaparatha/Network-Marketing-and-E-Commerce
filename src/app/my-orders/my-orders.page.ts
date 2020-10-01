import { Component, OnInit } from '@angular/core';
import { ConnectApiService } from '../connect-api.service';
import { Order } from '../models/order.model';
import { User } from '../models/user.model';
import { ShopServiceService } from '../shop/shop-service.service';
import { upload } from '../utils';

@Component({
  selector: 'app-my-orders',
  templateUrl: './my-orders.page.html',
  styleUrls: ['./my-orders.page.scss'],
})
export class MyOrdersPage implements OnInit {

  myOrders: Order[]
  user: User;
  path: string = upload
  constructor(private api: ShopServiceService, private userApi: ConnectApiService) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    let email = localStorage.getItem('email');
    this.userApi.onGetUser(email).subscribe(res => {
      this.user = res;
      this.api.onGetMyOrders(this.user.id).subscribe(ord => {
        this.myOrders = ord
      })
    })
  }

}
