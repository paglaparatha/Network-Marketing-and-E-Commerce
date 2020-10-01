import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ShopNavComponent } from '../shop/shop-nav/shop-nav.component';
import { RouterModule } from '@angular/router';
import { IonicModule } from '@ionic/angular';



@NgModule({
  declarations: [ShopNavComponent],
  imports: [
    CommonModule,
    RouterModule,
    IonicModule
  ],
  exports: [
    ShopNavComponent
  ]
})
export class CartIconModule { }
