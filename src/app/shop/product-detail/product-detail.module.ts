import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ProductDetailPageRoutingModule } from './product-detail-routing.module';

import { ProductDetailPage } from './product-detail.page';
import { CartIconModule } from 'src/app/cart-icon/cart-icon.module';
import { LoadingModuleModule } from 'src/app/loading-module/loading-module.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ProductDetailPageRoutingModule,
    CartIconModule,
    LoadingModuleModule
  ],
  declarations: [ProductDetailPage]
})
export class ProductDetailPageModule {}
