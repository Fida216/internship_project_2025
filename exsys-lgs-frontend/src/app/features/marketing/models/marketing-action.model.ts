import { TargetClient } from "./campaign.model";

// features/marketing/models/marketing-action.model.ts
export interface MarketingAction {
  id: string;
  title: string;
  channelType: 'email' | 'sms' | 'whatsapp' | 'push';
  content: string;
  campaignId?: string;
  campaignTitle?: string;
  createdAt: string;
  clients?: TargetClient[];
}

export interface MarketingActionResponse {
  marketingCampaign: {
    id: string;
    title: string;
    description: string;
    startDate: string;
    endDate: string;
    status: string;
    createdAt: string;
    updatedAt: string;
  };
  marketingActions: MarketingAction[];
  total: number;
}