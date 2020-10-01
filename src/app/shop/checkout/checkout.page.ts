import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { AlertController } from '@ionic/angular';
import { ConnectApiService } from 'src/app/connect-api.service';
import { Address } from 'src/app/models/address.model';
import { User } from 'src/app/models/user.model';
import { ShopServiceService } from '../shop-service.service';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.page.html',
  styleUrls: ['./checkout.page.scss'],
})
export class CheckoutPage implements OnInit {

  user: User;
  addresses: Address[];
  total: number;
  constructor(private userApi: ConnectApiService, private api: ShopServiceService, private alertCtrl: AlertController) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    let email = localStorage.getItem('email');
    this.userApi.onGetUser(email).subscribe(res => {
      this.user = res;
      this.onGetAddresses(this.user.id)
      this.total = 0
      this.api.cart.getValue().forEach(el => {
        this.total += el.Cost;
      })
    })
  }

  async presentAlert(head:string, msg: string) {
    const alert = await this.alertCtrl.create({
      header: head,
      message: msg,
      buttons: ['OK']
    });

    await alert.present();
  }

  onGetAddresses(id: number) {
    this.api.onGetAddresses(this.user.id).subscribe(addr => {
      this.addresses = addr;
    });
  }

  onAddAddress(form: NgForm) {
    let myForm = new FormData();

    for (const key in form.value) {
      if (Object.prototype.hasOwnProperty.call(form.value, key)) {
        const element = form.value[key];
        myForm.append(key, element)
      }
    }

    myForm.append('user_id', this.user.id.toString())
    myForm.append('add-address', 'true')

    this.api.onAddAddress(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert('Error', res.description)
      } else {
        let email = localStorage.getItem('email')
        this.onGetAddresses(this.user.id)
        this.presentAlert('Success', 'Address Successfully Added!')
      }
      form.reset();
      this.onGetAddresses(this.user.id)
    })
  }

  onCheckOut(form: NgForm) {
    let myForm = new FormData();

    myForm.append('address_id', form.value.address_id)
    myForm.append('user_id', this.user.id.toString());
    let cart = this.api.cart.getValue();
    let i = 0;
    cart.forEach(el => {
      myForm.append('product_id['+i+']', el.Product.id.toString());
      myForm.append('quantity['+i+']', el.Quantity.toString());
      i++;
    })

    myForm.append('count', cart.length.toString())
    myForm.append('add-order', 'true')

    this.api.onCheckOut(myForm).subscribe(res => {
      if (res.status == 'error') {
        this.presentAlert('Error', res.description)
      } else {
        this.onGetAddresses(this.user.id)
        this.presentAlert('Success', 'Order Successfully Placed!')
      }
      form.reset();
      this.api.cart.next([]);
      localStorage.removeItem('myCart')
    })

  }

}
