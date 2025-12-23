import { TargetClient } from "./campaign.model";

// features/marketing/models/quick-message.model.ts
export interface QuickMessage {
  id: string;
  title: string;
  channelType: 'email' | 'sms' | 'whatsapp' | 'push';
  content: string;
  clientCount: number;
  createdAt: string;
  clients?: TargetClient[];
}

export interface QuickMessageListResponse {
  quickMessages: QuickMessage[];
  total: number;
}

export interface QuickMessageDetailResponse {
  id: string;
  title: string;
  channelType: string;
  content: string;
  clients: TargetClient[];
}