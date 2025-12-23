import { Injectable } from '@angular/core';
import {
  HttpInterceptor,
  HttpRequest,
  HttpHandler,
  HttpEvent
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../services/auth.service';
import { throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  constructor(private authService: AuthService) {}


  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
  // Skip for auth routes
  if (req.url.includes('/auth/login')) {
    return next.handle(req);
  }

    const token = this.authService.getToken();
    
  
    if (token && !this.authService.isLoggedIn()) {
      this.authService.logout();
      return throwError(() => new Error('Session expired'));
  }

  if (token) {
    req = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
  }
  
  return next.handle(req).pipe(
    catchError((err) => {
      if (err.status === 401) {
        this.authService.logout();
      }
      return throwError(() => err);
    })
  );
}


}