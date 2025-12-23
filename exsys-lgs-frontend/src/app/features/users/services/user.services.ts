// features/users/services/user.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { 
  User, 
  CreateUserRequest, 
  UpdateUserRequest, 
  ChangePasswordRequest, 
  ResetPasswordRequest, 
  UserStatusRequest,
  ExchangeOfficeWithAgents,
  UserResponse
} from '../models/user.model';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = `${environment.API_BASE_URL}/users`;

  constructor(private http: HttpClient) {}

  getUsers(): Observable<User[]> {
    return this.http.get<User[]>(this.apiUrl);
  }

  getAgentsGroupedByOffice(): Observable<ExchangeOfficeWithAgents[]> {
    return this.http.get<ExchangeOfficeWithAgents[]>(`${this.apiUrl}/agents/grouped-by-exchange-office`);
  }

  createUser(userData: CreateUserRequest): Observable<UserResponse> {
    return this.http.post<UserResponse>(this.apiUrl, userData);
  }

  updateUser(userId: string, userData: UpdateUserRequest): Observable<UserResponse> {
    return this.http.put<UserResponse>(`${this.apiUrl}/update`, userData, {
      params: { userId }
    });
  }

  updateUserStatus(userId: string, statusData: UserStatusRequest): Observable<UserResponse> {
    return this.http.patch<UserResponse>(`${this.apiUrl}/status`, statusData, {
      params: { userId }
    });
  }

  changePassword(passwordData: ChangePasswordRequest): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.apiUrl}/change-password`, passwordData);
  }

  resetPassword(userId: string, passwordData: ResetPasswordRequest): Observable<{ message: string }> {
    return this.http.put<{ message: string }>(`${this.apiUrl}/reset-password`, passwordData, {
      params: { userId }
    });
  }
}