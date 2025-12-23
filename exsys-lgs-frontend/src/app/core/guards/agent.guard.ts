import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { Router } from "@angular/router";
import { AuthService } from "../services/auth.service";

@Injectable({ providedIn: 'root' })
export class AgentGuard implements CanActivate {
  constructor(private auth: AuthService, private router: Router) {}

  canActivate(): boolean {

    if (this.authService.isTokenExpired()) {
      this.authService.logout();
      return false;
    }

    const payload = this.auth.getUserInfo();
    if (payload?.role === 'agent') return true;
    this.router.navigate(['/unauthorized']);
    return false;
  }
}
