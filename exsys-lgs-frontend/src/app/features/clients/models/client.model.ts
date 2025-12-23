// features/clients/models/client.model.ts
export interface Client {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  whatsapp?: string;
  birthDate?: string;
  nationalId?: string;
  passport?: string;
  nationality: string;
  residence?: string;
  gender?: 'male' | 'female';
  acquisitionSource?: string;
  status: 'active' | 'inactive';
  currentSegment?: string;
  createdAt: string;
  updatedAt?: string;
  exchangeOffice?: {
    id: string;
    name: string;
  };
}

