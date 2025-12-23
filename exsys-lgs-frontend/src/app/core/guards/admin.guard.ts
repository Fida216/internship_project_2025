import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { Router } from "@angular/router";
import { AuthService } from "../services/auth.service";

@Injectable({ providedIn: 'root' })
export class AdminGuard implements CanActivate {
  constructor(private authService: AuthService, private router: Router) {}

  canActivate(): boolean {

    
    if (this.authService.isTokenExpired()) {
          this.authService.logout();
          return false;
    }


    const payload = this.authService.getUserInfo();
    if (payload?.role === 'admin') return true;
    this.router.navigate(['/unauthorized']);
    return false;
  }
}
