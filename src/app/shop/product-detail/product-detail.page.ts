import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Product } from 'src/app/models/product.model';
import { upload } from 'src/app/utils';
import { ShopServiceService } from '../shop-service.service';

@Component({
  selector: 'app-product-detail',
  templateUrl: './product-detail.page.html',
  styleUrls: ['./product-detail.page.scss'],
})
export class ProductDetailPage implements OnInit {

  id: number
  product: Product
  path: string = upload
  qty: number = 0;

  similiarProducts: Product[]
  slideOpts = {
    slidesPerView: 1,
    initialSlide: 0,
    speed: 400,
    autoplay: true,
    loop: true,
    autoplayDisableOnInteraction: false
  };
  constructor(private api: ShopServiceService, private activatedRoute: ActivatedRoute) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.activatedRoute.paramMap.subscribe(params => {
      this.id = +params.get('id')
      this.api.onGetProduct(this.id).subscribe(res => {
        this.product = res;
        let check = this.api.checkIfProductInCart(this.id)
        let checkLen = check.length
        this.qty = checkLen > 0 ? check[0].Quantity : 0
        this.api.onGetSimiliarProducts(res.id, res.category, res.company).subscribe(sp => {
          this.similiarProducts = sp;
        })
      })
    })
  }

  more() {
    this.qty++;
  }

  less() {
    this.qty == 0 ? this.qty : this.qty--;
  }

  onAddToCart() {
    this.api.onAddToCart(this.product, this.qty, this.product.price*this.qty)
  }

}
