import { Component, OnDestroy, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { ShopServiceService } from '../shop-service.service';

@Component({
  selector: 'app-shop-nav',
  templateUrl: './shop-nav.component.html',
  styleUrls: ['./shop-nav.component.scss'],
})
export class ShopNavComponent implements OnInit, OnDestroy {

  cart: Subscription;
  cartLen: number;
  constructor(private api: ShopServiceService) { }

  ngOnInit() {
    this.cart = this.api.cart.subscribe(data => {
      this.cartLen = data.length
    })
  }

  ngOnDestroy() {
    this.cart.unsubscribe()
  }

}
