// features/offices/models/office.model.ts
export interface ExchangeOffice {
  id: string;
  name: string;
  address: string;
  email: string;
  phone: string;
  owner: string;
  status: 'active' | 'inactive';
  createdAt: string;
  updatedAt?: string;
}

export interface PaginatedOfficeResponse {
  exchangeOffices: ExchangeOffice[];
  total: number;
  filters?: {
    status?: string;
    id?: string;
  };
}