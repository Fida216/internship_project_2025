import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(private authService: AuthService, private router: Router) {}

  canActivate(): boolean {
    console.log('AuthGuard: Checking if user is logged in');
    console.log(this.authService.isLoggedIn())
    if (this.authService.isLoggedIn()) {
      return true;
    } else {
      // TODO: Redirect to login page or show a message
      this.router.navigate(['/user/login']);
      return false;
    }
  }
}
