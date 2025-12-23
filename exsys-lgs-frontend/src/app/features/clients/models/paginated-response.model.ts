// features/clients/models/paginated-response.model.ts
export interface PaginatedResponse<T> {
  clients: T[];
  total: number;
  page: number;
  limit: number;
}