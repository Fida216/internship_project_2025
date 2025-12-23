import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';


import { environment } from '../../environments/environment';



// Define TypeScript interfaces for your data models
interface LoginData {
  email: string;
  password: string;
}

interface ResetPasswordData {
  token: string;
  password: string;
}

interface UserProfile {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
  exchangeOffice?: {
    id: string;
    name: string;
  };
  // Add other profile fields as needed
}

interface InvitationPayload {
  password: string;
  // Add other invitation acceptance fields as needed
}

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private baseUrl: string;

  constructor(private http: HttpClient) {
    this.baseUrl = environment.API_BASE_URL;
    
    if (!this.baseUrl) {
      console.error('API base URL is not configured!');
    }
  }

  login(data: LoginData): Observable<{ token: string; user: UserProfile }> {
    return this.http.post<{ token: string; user: UserProfile }>(`${this.baseUrl}/auth/login`, data)
      .pipe(
        catchError(this.handleError)
      );
  }

  sendResetPassword(email: string): Observable<void> {
    return this.http.post<void>(`${this.baseUrl}/auth/forgot-password`, { email })
      .pipe(
        catchError(this.handleError)
      );
  }

  resetPassword(data: ResetPasswordData): Observable<void> {
    return this.http.post<void>(`${this.baseUrl}/auth/reset-password`, data)
      .pipe(
        catchError(this.handleError)
      );
  }

  validateInvitationToken(token: string): Observable<{ valid: boolean; email?: string }> {
    return this.http.get<{ valid: boolean; email?: string }>(`${this.baseUrl}/invitation/${token}`)
      .pipe(
        catchError(this.handleError)
      );
  }

  acceptInvitation(token: string, payload: InvitationPayload): Observable<{ token: string }> {
    return this.http.post<{ token: string }>(`${this.baseUrl}/invitation/accept/${token}`, payload)
      .pipe(
        catchError(this.handleError)
      );
  }

  getUserProfile(): Observable<{"user":UserProfile}> {
    return this.http.get<{"user":UserProfile}>(`${this.baseUrl}/auth/me`)
      .pipe(
        catchError(this.handleError)
      );
  }

  getProfilePicture(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/user/profile-picture`, { 
      responseType: 'blob' 
    }).pipe(
      catchError(this.handleError)
    );
  }

  private handleError(error: HttpErrorResponse): Observable<never> {
    let errorMessage = 'An unknown error occurred!';
    
    if (error.error instanceof ErrorEvent) {
      // Client-side error
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // Server-side error
      errorMessage = `Error Code: ${error.status}\nMessage: ${error.message}`;
      
      // You could add more specific error messages based on status codes
      if (error.status === 401) {
        errorMessage = 'Unauthorized - Please login again';
      } else if (error.status === 403) {
        errorMessage = 'Forbidden - You don\'t have permission';
      } else if (error.status === 404) {
        errorMessage = 'Resource not found';
      }
    }
    
    console.error(errorMessage);
    return throwError(() => new Error(errorMessage));
  }
}