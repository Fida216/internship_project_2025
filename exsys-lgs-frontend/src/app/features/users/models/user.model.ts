// features/users/models/user.model.ts
export interface User {
  id: string;
  lastName: string;
  firstName: string;
  phone: string;
  role: 'admin' | 'agent';
  status: 'active' | 'inactive';
  email: string;
  createdAt: string;
  exchangeOffice?: {
    id: string;
    name: string;
  };
}

export interface CreateUserRequest {
  lastName: string;
  firstName: string;
  phone: string;
  role: 'admin' | 'agent';
  email: string;
  password: string;
  exchangeOfficeId?: string;
  status: 'active' | 'inactive';
}

export interface UpdateUserRequest {
  lastName?: string;
  firstName?: string;
  phone?: string;
  email?: string;
  status?: 'active' | 'inactive';
}

export interface ChangePasswordRequest {
  oldPassword: string;
  newPassword: string;
}

export interface ResetPasswordRequest {
  newPassword: string;
}

export interface UserStatusRequest {
  status: 'active' | 'inactive';
}

export interface ExchangeOfficeWithAgents {
  exchangeOffice: {
    id: string;
    name: string;
    address: string;
    phone: string;
    email: string;
    owner: string;
    status: string;
    createdAt: string;
  };
  agents: User[];
}

export interface UserResponse {
  message: string;
  user: User;
}