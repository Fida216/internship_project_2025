import { Injectable } from '@angular/core';
import { jwtDecode } from 'jwt-decode';
import { JwtPayload } from './jwt-payload.interface';





@Injectable({
  providedIn: 'root'
})
export class AuthService {
  
  private tokenKey = 'auth-token';
  constructor() { }


  getUserInfo(): JwtPayload | null {
    const token = this.getToken();
    if (!token) return null;
  
    try {
      return jwtDecode<JwtPayload>(token);
    } catch (e) {
      console.error('Invalid token:', e);
      return null;
    }
  }


  getUserRole(): string | null {
    const userInfo = this.getUserInfo();
    return userInfo ? userInfo.role : null;
  }

  isAdmin(): boolean {
    const userRole = this.getUserRole();
    return userRole === 'admin';
  }

  setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  isLoggedIn(): boolean {
    const token = this.getToken();
    if (!token) return false;
    try {
      const payload = this.getUserInfo();
      return payload? payload.exp * 1000 > Date.now() : false;
    } catch {
      return false;
    }
  }


  clearToken(): void {
    localStorage.removeItem(this.tokenKey);
  }

  isNotLoggedIn():boolean{
    return !this.isLoggedIn();
  }

  logout(): void {
    this.clearToken();
    console.log('User logged out');
    // Optionally, you can redirect to the login page or show a message
    location.href = '/user/login';
  }
}
