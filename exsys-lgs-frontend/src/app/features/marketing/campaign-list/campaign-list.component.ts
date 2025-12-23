// features/marketing/campaign-list/campaign-list.component.ts
import { Component, OnInit } from '@angular/core';
import { MarketingService } from '../services/marketing.service';
import { CampaignListItem, CampaignListResponse } from '../models/campaign.model';
import { MatDialog } from '@angular/material/dialog';
import { CampaignFormComponent } from '../campaign-form/campaign-form.component';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-campaign-list',
  templateUrl: './campaign-list.component.html',
  standalone: false,
  styleUrls: ['./campaign-list.component.css']
})
export class CampaignListComponent implements OnInit {
  campaigns: CampaignListItem[] = [];
  isLoading = false;
  statusFilter = '';

  displayedColumns: string[] = [
    'title',
    'status',
    'startDate',
    'endDate',
    'targetClientCount',
    'createdAt',
    'actions'
  ];

  constructor(
    private marketingService: MarketingService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.loadCampaigns();
  }

  loadCampaigns(): void {
    this.isLoading = true;
    this.marketingService.getCampaigns().subscribe({
      next: (response: CampaignListResponse) => {
        this.campaigns = response.campaigns;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load campaigns', 'Close', { duration: 3000 });
      }
    });
  }

  openCreateDialog(): void {
    const dialogRef = this.dialog.open(CampaignFormComponent, {
      width: '800px',
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadCampaigns();
      }
    });
  }

  updateCampaignStatus(campaignId: string, currentStatus: string): void {
    let newStatus = 'active';
    if (currentStatus === 'active') {
      newStatus = 'completed';
    } else if (currentStatus === 'draft') {
      newStatus = 'active';
    }

    this.isLoading = true;
    this.marketingService.updateCampaignStatus(campaignId, newStatus).subscribe({
      next: () => {
        this.loadCampaigns();
        this.snackBar.open(`Campaign status updated to ${newStatus}`, 'Close', { duration: 3000 });
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to update campaign status', 'Close', { duration: 3000 });
      }
    });
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

  getStatusIcon(status: string): string {
    switch (status) {
      case 'active': return 'play_arrow';
      case 'completed': return 'check_circle';
      case 'draft': return 'edit';
      case 'cancelled': return 'cancel';
      default: return 'help';
    }
  }
}