export interface JwtPayload {
    user_id: number;
    email: string;
    role: string;
    iat: number;   // Issued at
    exp: number;   // Expiration
  }