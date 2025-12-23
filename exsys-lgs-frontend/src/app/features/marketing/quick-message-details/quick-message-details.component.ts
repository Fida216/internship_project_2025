// features/marketing/quick-message-details/quick-message-details.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MarketingService } from '../services/marketing.service';
import { QuickMessageDetailResponse } from '../models/quick-message.model';
import { Location } from '@angular/common';
import { MatSnackBar } from '@angular/material/snack-bar';
import { TargetClient } from '../models/campaign.model';


@Component({
  selector: 'app-quick-message-details',
  templateUrl: './quick-message-details.component.html',
  standalone: false,
  styleUrls: ['./quick-message-details.component.css']
})
export class QuickMessageDetailsComponent implements OnInit {
  quickMessage: QuickMessageDetailResponse | null = null;
  isLoading = false;

  constructor(
    private route: ActivatedRoute,
    private marketingService: MarketingService,
    private location: Location,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.loadQuickMessage();
  }

  loadQuickMessage(): void {
    const messageId = this.route.snapshot.paramMap.get('id');
    if (!messageId) return;

    this.isLoading = true;
    this.marketingService.getQuickMessage(messageId).subscribe({
      next: (response) => {
        this.quickMessage = response;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load quick message details', 'Close', { duration: 3000 });
        this.location.back();
      }
    });
  }

  goBack(): void {
    this.location.back();
  }

  getChannelIcon(channelType: string): string {
    switch (channelType) {
      case 'email': return 'email';
      case 'sms': return 'sms';
      case 'whatsapp': return 'chat';
      case 'push': return 'notifications';
      default: return 'message';
    }
  }

  getChannelColor(channelType: string): string {
    switch (channelType) {
      case 'email': return 'primary';
      case 'sms': return 'accent';
      case 'whatsapp': return 'warn';
      case 'push': return 'basic';
      default: return 'basic';
    }
  }
}