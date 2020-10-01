import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { base, key } from '../utils';
import { map } from 'rxjs/operators'
import { Product } from '../models/product.model';
import { BehaviorSubject } from 'rxjs';
import { Address } from '../models/address.model';
import { ApiResponse } from '../models/response.model';
import { Order } from '../models/order.model';

@Injectable({
  providedIn: 'root'
})
export class ShopServiceService {

  constructor(private http: HttpClient) { }

  cart = new BehaviorSubject<{ Product: Product, Quantity: number, Cost: number }[]>(localStorage.getItem('myCart') ? JSON.parse(localStorage.getItem('myCart')) : [])

  onGetSliders() {
    return this.http.get<{ id: number, image: string, loaded: boolean }[]>(`${base}?api-key=${key}&get-sliders=true`)
      .pipe(
        map(elmnt => {
          let data = [];
          elmnt.forEach(el => {
            el.loaded = false
            data.push(el)
          });

          return data
        })
      )
  }

  onGetCategories() {
    return this.http.get<{ id: number, image: string, name: string, loaded: boolean }[]>(`${base}?api-key=${key}&get-categories=true`)
      .pipe(
        map(elmnt => {
          let data = [];
          elmnt.forEach(el => {
            el.loaded = false
            data.push(el)
          });

          return data
        })
      )
  }

  onGetDemoProducts(company: string) {
    return this.http.get<Product[]>(`${base}?api-key=${key}&company=${company}&view-demo-products=true`)
      .pipe(
        map(elmnt => {
          let data = [];
          elmnt.forEach(el => {
            el.loaded = false
            data.push(el)
          });

          return data
        })
      )
  }

  onGetProductsByCategory(page: number, category: string, company: string) {
    return this.http.get<{ Products: Product[], Pages: number }>(`${base}?api-key=${key}&company=${company}&page=${page}&category=${category}&get-product-by-category=true`)
  }

  onGetProduct(id: number) {
    return this.http.get<Product>(`${base}?api-key=${key}&id=${id}&get-product=true`)
  }

  onAddToCart(product: Product, qty: number, cost: number) {
    let temp = { Product: product, Quantity: qty, Cost: cost }
    let tempCart = this.cart.getValue();
    let flag = true;
    tempCart.forEach(elmnt => {
      if (elmnt.Product.id === temp.Product.id) {
        elmnt.Quantity = temp.Quantity;
        elmnt.Cost += +temp.Cost;
        flag = false;
      }
    })
    if (flag) {
      tempCart.push(temp)
    }
    this.cart.next(tempCart);

    localStorage.setItem('myCart', JSON.stringify(tempCart))
  }

  onRemoveProduct(pro: { Product: Product, Quantity: number, Cost: number }) {
    let tempCart = this.cart.getValue();
    tempCart = tempCart.filter(elmnt => elmnt.Product.id != pro.Product.id);
    this.cart.next(tempCart);
    localStorage.setItem('myCart', JSON.stringify(tempCart))
  }

  onGetSimiliarProducts(id: number, category: string, company: string) {
    return this.http.get<Product[]>(`${base}?api-key=${key}&company=${company}&id=${id}&category=${category}&get-similiar-products=true`)
  }

  checkIfProductInCart(id: number) {
    return this.cart.getValue().filter(elmnt => elmnt.Product.id === id)
  }

  onGetAddresses(id: number) {
    return this.http.get<Address[]>(`${base}?api-key=${key}&id=${id}&get-addresses=true`)
  }

  onAddAddress(form: FormData) {
    return this.http.post<ApiResponse>(`${base}?api-key=${key}`, form)
  }

  onCheckOut(form: FormData) {
    return this.http.post<ApiResponse>(`${base}?api-key=${key}`, form)
  }

  onGetMyOrders(id: number) {
    return this.http.get<Order[]>(`${base}?api-key=${key}&id=${id}&get-my-orders=true`)
  }
}
