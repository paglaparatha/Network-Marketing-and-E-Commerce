import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ShopPageRoutingModule } from './shop-routing.module';

import { ShopPage } from './shop.page';

import { DemoProductsComponent } from '../demo-products/demo-products.component';
import { LoadingModuleModule } from '../loading-module/loading-module.module';
import { CartIconModule } from '../cart-icon/cart-icon.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ShopPageRoutingModule,
    LoadingModuleModule,
    CartIconModule
  ],
  declarations: [ShopPage, DemoProductsComponent]
})
export class ShopPageModule {}
