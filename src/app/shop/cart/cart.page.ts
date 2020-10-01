import { Component, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { Product } from 'src/app/models/product.model';
import { upload } from 'src/app/utils';
import { ShopServiceService } from '../shop-service.service';

@Component({
  selector: 'app-cart',
  templateUrl: './cart.page.html',
  styleUrls: ['./cart.page.scss'],
})
export class CartPage implements OnInit {

  totalCost = 0;
  path: string = upload
  cart: { Product: Product, Quantity: number, Cost: number }[];
  sub: Subscription;
  constructor(private api: ShopServiceService) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.sub =  this.api.cart.subscribe(data => {
      this.cart = data
      console.log(this.cart)
      this.totalCost = 0;
      this.cart.forEach(elmnt => {
        this.totalCost += elmnt.Cost
      })
    })
  }

  ionViewDidLeave() {
    this.sub.unsubscribe()
  }

  onRemoveProduct(pro: { Product: Product, Quantity: number, Cost: number }) {
    this.api.onRemoveProduct(pro)
  }
}
