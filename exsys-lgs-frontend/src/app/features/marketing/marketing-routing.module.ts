// features/marketing/marketing-routing.module.ts
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CampaignListComponent } from './campaign-list/campaign-list.component';
import { CampaignDetailsComponent } from './campaign-details/campaign-details.component';
import { QuickMessageListComponent } from './quick-message-list/quick-message-list.component';
import { QuickMessageDetailsComponent } from './quick-message-details/quick-message-details.component';

const routes: Routes = [
  { path: 'campaigns', component: CampaignListComponent },
  { path: 'campaigns/:id', component: CampaignDetailsComponent },
  { path: 'messages', component: QuickMessageListComponent },
  { path: 'messages/:id', component: QuickMessageDetailsComponent }, // Add this
  { path: '', redirectTo: 'campaigns', pathMatch: 'full' }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MarketingRoutingModule { }