// features/marketing/campaign-details/campaign-details.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MarketingService } from '../services/marketing.service';
import { CampaignDetailResponse } from '../models/campaign.model';
import { Location } from '@angular/common';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog } from '@angular/material/dialog';
import { MarketingActionFormComponent } from '../marketing-action-form/marketing-action-form.component';

@Component({
  selector: 'app-campaign-details',
  templateUrl: './campaign-details.component.html',
  standalone: false,
  styleUrls: ['./campaign-details.component.css']
})
export class CampaignDetailsComponent implements OnInit {
  campaign: CampaignDetailResponse | null = null;
  isLoading = false;

  constructor(
    private route: ActivatedRoute,
    private marketingService: MarketingService,
    private location: Location,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.loadCampaign();
  }

  loadCampaign(): void {
    const campaignId = this.route.snapshot.paramMap.get('id');
    if (!campaignId) return;

    this.isLoading = true;
    this.marketingService.getCampaignDetails(campaignId).subscribe({
      next: (response) => {
        this.campaign = response;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load campaign details', 'Close', { duration: 3000 });
        this.location.back();
      }
    });
  }

  openCreateActionDialog(): void {
    if (!this.campaign) return;

    const dialogRef = this.dialog.open(MarketingActionFormComponent, {
      width: '600px',
      disableClose: true,
      data: { campaignId: this.campaign.id }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadCampaign();
      }
    });
  }

  goBack(): void {
    this.location.back();
  }

  getStatusColor(status: string): string {
    switch (status) {
      case 'active': return 'primary';
      case 'completed': return 'accent';
      case 'draft': return 'basic';
      case 'cancelled': return 'warn';
      default: return 'basic';
    }
  }
}