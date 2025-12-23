// src/app/core/services/country.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { Country } from '../models/country.model';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class CountryService {
  private countriesCache: Country[] | null = null;
  private apiUrl = `${environment.API_BASE_URL}/countries`;

  constructor(private http: HttpClient) {}

  getCountries(): Observable<Country[]> {
    if (this.countriesCache) {
      return of(this.countriesCache);
    }

    return this.http.get<{ countries: Country[] }>(this.apiUrl).pipe(
      map(response => {
        this.countriesCache = response.countries;
        return response.countries;
      })
    );
  }

  getCountryByNationality(nationality: string): Observable<Country | undefined> {
    return this.getCountries().pipe(
      map(countries => countries.find(c => c.nationality === nationality))
    );
  }

  private getStaticCountries(): Country[] {
    return [
      { id: '1', code: 'FR', name: 'France', nationality: 'French', phoneCode: '+33' },
      { id: '2', code: 'MA', name: 'Morocco', nationality: 'Moroccan', phoneCode: '+212' },
      { id: '3', code: 'US', name: 'United States', nationality: 'American', phoneCode: '+1' },
      { id: '4', code: 'GB', name: 'United Kingdom', nationality: 'British', phoneCode: '+44' },
      { id: '5', code: 'DE', name: 'Germany', nationality: 'German', phoneCode: '+49' },
      { id: '6', code: 'ES', name: 'Spain', nationality: 'Spanish', phoneCode: '+34' },
      { id: '7', code: 'IT', name: 'Italy', nationality: 'Italian', phoneCode: '+39' },
      { id: '8', code: 'DZ', name: 'Algeria', nationality: 'Algerian', phoneCode: '+213' },
      { id: '9', code: 'TN', name: 'Tunisia', nationality: 'Tunisian', phoneCode: '+216' },
      { id: '10', code: 'EG', name: 'Egypt', nationality: 'Egyptian', phoneCode: '+20' },
      { id: '11', code: 'SA', name: 'Saudi Arabia', nationality: 'Saudi', phoneCode: '+966' },
      { id: '12', code: 'AE', name: 'United Arab Emirates', nationality: 'Emirati', phoneCode: '+971' },
      { id: '13', code: 'QA', name: 'Qatar', nationality: 'Qatari', phoneCode: '+974' },
      { id: '14', code: 'KW', name: 'Kuwait', nationality: 'Kuwaiti', phoneCode: '+965' },
    ];
  }
}