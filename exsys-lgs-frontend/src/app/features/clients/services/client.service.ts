// features/clients/services/client.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Client } from '../models/client.model';
import { PaginatedResponse } from '../models/paginated-response.model';

@Injectable({
  providedIn: 'root'
})
export class ClientService {
  private apiUrl = `${environment.API_BASE_URL}/clients`;

  constructor(private http: HttpClient) {}

  getClients(params: any, isMyClients = false): Observable<PaginatedResponse<Client>> {
    const endpoint = isMyClients ? `${this.apiUrl}/my-clients` : this.apiUrl;
    let httpParams = new HttpParams()
      .set('page', params.page.toString())
      .set('limit', params.limit.toString());

    if (params.search) {
      httpParams = httpParams.set('search', params.search);
    }
    if (params.status) {
      httpParams = httpParams.set('status', params.status);
    }
    if (params.nationality) {
      httpParams = httpParams.set('nationality', params.nationality);
    }
    if (params.gender) {
      httpParams = httpParams.set('gender', params.gender);
    }
    if (params.acquisitionSource) {
      httpParams = httpParams.set('acquisitionSource', params.acquisitionSource);
    }
    return this.http.get<PaginatedResponse<Client>>(endpoint, { params: httpParams });
  }

  getClientDetails(clientId: string): Observable<{"client":Client}> {
    return this.http.get<{"client":Client}>(`${this.apiUrl}/details`, {
      params: { clientId }
    });
  }

  createClient(clientData: any): Observable<Client> {
    return this.http.post<Client>(this.apiUrl, clientData);
  }

  updateClient(clientId: string, clientData: any): Observable<Client> {
    return this.http.put<Client>(this.apiUrl, clientData, {
      params: { clientId }
    });
  }

  deleteClient(clientId: string): Observable<any> {
    return this.http.delete(this.apiUrl, {
      params: { clientId }
    });
  }

  getClientsByOffice(params: any, exchangeOfficeId: string): Observable<PaginatedResponse<Client>> {
  let httpParams = new HttpParams()
    .set('page', params.page.toString())
    .set('limit', params.limit.toString())
    .set('exchangeOfficeId', exchangeOfficeId);

  if (params.search) httpParams = httpParams.set('search', params.search);
  if (params.status) httpParams = httpParams.set('status', params.status);
  if (params.nationality) httpParams = httpParams.set('nationality', params.nationality);
  if (params.gender) httpParams = httpParams.set('gender', params.gender);
  if (params.acquisitionSource) httpParams = httpParams.set('acquisitionSource', params.acquisitionSource);

  return this.http.get<PaginatedResponse<Client>>(`${this.apiUrl}/by-office`, { params: httpParams });
}

getClientsGrouped(): Observable<any> {
  return this.http.get<any>(`${this.apiUrl}/groupby_exchange_office`);
}



}