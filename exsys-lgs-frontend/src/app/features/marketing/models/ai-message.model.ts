// features/marketing/models/ai-message.model.ts
export interface AIGenerateRequest {
  message: string;
  language: string;
}

export interface AIGenerateResponse {
  success: boolean;
  generatedMessage?: string;
  improvedMessage?: string;
  errors?: string[];
  error?: string;
}