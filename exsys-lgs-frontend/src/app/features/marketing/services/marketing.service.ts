// features/marketing/services/marketing.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { CampaignListResponse, CampaignDetailResponse } from '../models/campaign.model';
import { MarketingAction, MarketingActionResponse } from '../models/marketing-action.model';
import { QuickMessageDetailResponse, QuickMessageListResponse } from '../models/quick-message.model';
import { AIGenerateRequest, AIGenerateResponse } from '../models/ai-message.model';



@Injectable({
  providedIn: 'root'
})
export class MarketingService {
  private apiUrl = `${environment.API_BASE_URL}`;

  constructor(private http: HttpClient) {}

  // Campaign endpoints
  getCampaigns(): Observable<CampaignListResponse> {
    return this.http.get<CampaignListResponse>(`${this.apiUrl}/marketing-campaigns/list`);
  }

  getCampaignDetails(campaignId: string): Observable<CampaignDetailResponse> {
    return this.http.get<CampaignDetailResponse>(`${this.apiUrl}/marketing-campaigns`, {
      params: { campaignId }
    });
  }

  createCampaign(campaignData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/marketing-campaigns`, campaignData);
  }

  updateCampaignStatus(campaignId: string, status: string): Observable<any> {
    return this.http.patch(`${this.apiUrl}/marketing-campaigns/status`, { status }, {
      params: { campaignId }
    });
  }

  addTargetClients(campaignId: string, clientIds: string[]): Observable<any> {
    return this.http.post(`${this.apiUrl}/marketing-campaigns/add-target-clients`, { clientIds }, {
      params: { campaignId }
    });
  }

  removeTargetClients(campaignId: string, clientIds: string[]): Observable<any> {
    return this.http.delete(`${this.apiUrl}/marketing-campaigns/remove-target-clients`, {
      params: { campaignId },
      body: { clientIds }
    });
  }

  // Marketing Action endpoints
  getMarketingAction(actionId: string): Observable<MarketingAction> {
    return this.http.get<MarketingAction>(`${this.apiUrl}/agent/marketing-action`, {
      params: { id: actionId }
    });
  }

  createMarketingAction(actionData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/agent/marketing-action`, actionData);
  }

  getCampaignMarketingActions(campaignId: string): Observable<MarketingActionResponse> {
    return this.http.get<MarketingActionResponse>(`${this.apiUrl}/agent/marketing-actions/by-campaign`, {
      params: { campaignId }
    });
  }

  // Quick Message endpoints
  getQuickMessage(messageId: string): Observable<QuickMessageDetailResponse> {
    return this.http.get<QuickMessageDetailResponse>(`${this.apiUrl}/agent/quick-message`, {
      params: { id: messageId }
    });
  }

  createQuickMessage(messageData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/agent/quick-message`, messageData);
  }

  getQuickMessages(): Observable<QuickMessageListResponse> {
    return this.http.get<QuickMessageListResponse>(`${this.apiUrl}/agent/quick-messages`);
  }

  // AI endpoints
  generateOfferMessage(request: AIGenerateRequest): Observable<AIGenerateResponse> {
    return this.http.post<AIGenerateResponse>(`${this.apiUrl}/ollama/generate-offer-message`, request);
  }

  improveMessage(request: AIGenerateRequest): Observable<AIGenerateResponse> {
    return this.http.post<AIGenerateResponse>(`${this.apiUrl}/ollama/improve-message`, request);
  }
}