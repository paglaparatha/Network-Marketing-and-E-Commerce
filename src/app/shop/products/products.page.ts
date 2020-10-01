import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ConnectApiService } from 'src/app/connect-api.service';
import { Product } from 'src/app/models/product.model';
import { User } from 'src/app/models/user.model';
import { upload } from 'src/app/utils';
import { ShopServiceService } from '../shop-service.service';

@Component({
  selector: 'app-products',
  templateUrl: './products.page.html',
  styleUrls: ['./products.page.scss'],
})
export class ProductsPage implements OnInit {

  page: number;
  category: string;
  totalPages: number;
  products: Product[];
  path: string = upload
  user: User;
  constructor(private api: ShopServiceService, private activatedRoute: ActivatedRoute, private homeApi: ConnectApiService) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.activatedRoute.paramMap.subscribe(params => {
      this.page = +params.get('page')
      this.category = params.get('category');

      let email = localStorage.getItem('email');
      this.homeApi.onGetUser(email).subscribe(res => {
        this.user = res
        this.api.onGetProductsByCategory(this.page, this.category, this.user.company).subscribe(res => {
          this.totalPages = res.Pages
          this.products = res.Products
        })
      })
    })
  }

}
