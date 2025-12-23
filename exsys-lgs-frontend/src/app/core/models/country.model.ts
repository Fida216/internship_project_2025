// src/app/core/models/country.model.ts
export interface Country {
  id: string;
  code: string;
  name: string;
  nationality: string;
  phoneCode?: string;
  flag?: string;
}