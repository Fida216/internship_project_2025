// features/marketing/quick-message-list/quick-message-list.component.ts
import { Component, OnInit } from '@angular/core';
import { MarketingService } from '../services/marketing.service';
import { QuickMessage, QuickMessageListResponse } from '../models/quick-message.model';
import { MatDialog } from '@angular/material/dialog';
import { QuickMessageFormComponent } from '../quick-message-form/quick-message-form.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Router } from '@angular/router';


@Component({
  selector: 'app-quick-message-list',
  templateUrl: './quick-message-list.component.html',
  standalone: false,
  styleUrls: ['./quick-message-list.component.css']
})
export class QuickMessageListComponent implements OnInit {
  quickMessages: QuickMessage[] = [];
  isLoading = false;

  displayedColumns: string[] = [
    'title',
    'channelType',
    'clientCount',
    'createdAt',
    'actions'
  ];

  constructor(
    private marketingService: MarketingService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadQuickMessages();
  }

  viewMessageDetails(messageId: string): void {
    this.router.navigate(['/marketing/messages', messageId]);
  }

  loadQuickMessages(): void {
    this.isLoading = true;
    this.marketingService.getQuickMessages().subscribe({
      next: (response: QuickMessageListResponse) => {
        this.quickMessages = response.quickMessages;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load quick messages', 'Close', { duration: 3000 });
      }
    });
  }

  openCreateDialog(): void {
    const dialogRef = this.dialog.open(QuickMessageFormComponent, {
      width: '800px',
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadQuickMessages();
      }
    });
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
}