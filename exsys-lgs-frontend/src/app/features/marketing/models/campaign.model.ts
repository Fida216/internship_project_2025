import { MarketingAction } from "./marketing-action.model";

// features/marketing/models/campaign.model.ts
export interface MarketingCampaign {
  id: string;
  title: string;
  description: string;
  status: 'draft' | 'active' | 'completed' | 'cancelled';
  startDate: string;
  endDate: string;
  createdAt: string;
  updatedAt?: string;
  targetClientCount?: number;
  targetClients?: TargetClient[];
  marketingActions?: MarketingAction[];
}

export interface TargetClient {
  id: string;
  firstName: string;
  lastName: string;
}

export interface CampaignListItem {
  id: string;
  title: string;
  status: string;
  startDate: string;
  endDate: string;
  targetClientCount: number;
  createdAt: string;
}

export interface CampaignListResponse {
  campaigns: CampaignListItem[];
  total: number;
}

export interface CampaignDetailResponse {
  id: string;
  title: string;
  description: string;
  status: string;
  startDate: string;
  endDate: string;
  createdAt: string;
  targetClients: TargetClient[];
  marketingActions: MarketingAction[];
}