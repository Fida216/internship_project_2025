// features/offices/services/offices.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ExchangeOffice, PaginatedOfficeResponse } from '../models/office.model';

@Injectable({
  providedIn: 'root'
})
export class OfficesService {
  private apiUrl = `${environment.API_BASE_URL}/exchange-offices`;

  constructor(private http: HttpClient) {}

  getExchangeOffices(params?: any): Observable<PaginatedOfficeResponse> {
    let httpParams = new HttpParams();
    
    if (params) {
      if (params.status) {
        httpParams = httpParams.set('status', params.status);
      }
      if (params.id) {
        httpParams = httpParams.set('id', params.id);
      }
      if (params.page) {
        httpParams = httpParams.set('page', params.page.toString());
      }
      if (params.limit) {
        httpParams = httpParams.set('limit', params.limit.toString());
      }
    }

    return this.http.get<PaginatedOfficeResponse>(this.apiUrl, { params: httpParams });
  }

  getExchangeOfficeDetails(officeId: string): Observable<{exchangeOffice: ExchangeOffice}> {
    return this.http.get<{exchangeOffice: ExchangeOffice}>(this.apiUrl, {
      params: { id: officeId }
    });
  }

  createOffice(officeData: any): Observable<any> {
    return this.http.post(this.apiUrl, officeData);
  }

  updateOffice(officeId: string, officeData: any): Observable<any> {
    return this.http.put(this.apiUrl, officeData, {
      params: { id: officeId }
    });
  }

  deleteOffice(officeId: string): Observable<any> {
    return this.http.delete(this.apiUrl, {
      params: { id: officeId }
    });
  }

  updateOfficeStatus(officeId: string, status: string): Observable<any> {
    return this.http.patch(`${this.apiUrl}/status`, { status }, {
      params: { id: officeId }
    });
  }

  getMyOffice(): Observable<{exchangeOffice: ExchangeOffice}> {
    return this.http.get<{exchangeOffice: ExchangeOffice}>(`${this.apiUrl}/my-office`);
  }
}