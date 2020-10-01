import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Company } from './models/company.model';
import { ApiResponse } from './models/response.model';
import { User } from './models/user.model';
import { base, key } from './utils';

@Injectable({
  providedIn: 'root'
})
export class ConnectApiService {

  constructor(private http: HttpClient, private router: Router) { }

  onGetCompanies() {
    return this.http.get<Company[]>(`${base}?api-key=${key}&get-companies=true`)
  }

  onSignUp(form: FormData) {
    return this.http.post<ApiResponse>(`${base}?api-key=${key}`, form)
  }

  onLogin(form: FormData) {
    return this.http.post<ApiResponse>(`${base}?api-key=${key}`, form)
  }

  onLogout() {
    localStorage.removeItem('email');
    this.router.navigate(['/login'])
  }

  onGetUser(email: string) {
    return this.http.get<User>(`${base}?api-key=${key}&get-user=${email}`)
  }

  onEditUser(form: FormData) {
    return this.http.post<ApiResponse>(`${base}?api-key=${key}`, form)
  }

  onChangePassword(form: FormData) {
    return this.http.post<ApiResponse>(`${base}?api-key=${key}`, form)
  }
  
}
