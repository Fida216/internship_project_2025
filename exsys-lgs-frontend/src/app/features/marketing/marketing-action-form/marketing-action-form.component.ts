// features/marketing/marketing-action-form/marketing-action-form.component.ts
import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MarketingService } from '../services/marketing.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-marketing-action-form',
  templateUrl: './marketing-action-form.component.html',
  standalone: false,
  styleUrls: ['./marketing-action-form.component.css']
})
export class MarketingActionFormComponent implements OnInit {
  actionForm!: FormGroup;
  isLoading = false;
  campaignId: string;

  channelTypes = [
    { value: 'email', label: 'Email' },
    { value: 'sms', label: 'SMS' },
    { value: 'whatsapp', label: 'WhatsApp' },
    { value: 'push', label: 'Push Notification' }
  ];

  constructor(
    private fb: FormBuilder,
    private marketingService: MarketingService,
    private dialogRef: MatDialogRef<MarketingActionFormComponent>,
    private snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {
    this.campaignId = data.campaignId;
  }

  ngOnInit(): void {
    this.initForm();
  }

  initForm(): void {
    this.actionForm = this.fb.group({
      title: ['', [Validators.required, Validators.maxLength(100)]],
      channelType: ['email', Validators.required],
      content: ['', [Validators.required, Validators.maxLength(1000)]],
      campaignId: [this.campaignId, Validators.required]
    });
  }

  onSubmit(): void {
    if (this.actionForm.invalid) {
      return;
    }

    this.isLoading = true;
    const actionData = this.actionForm.value;

    this.marketingService.createMarketingAction(actionData).subscribe({
      next: () => {
        this.isLoading = false;
        this.snackBar.open('Marketing action created successfully', 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to create marketing action', 'Close', { duration: 3000 });
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  improveMessage(): void {
    const content = this.actionForm.get('content')?.value;
    if (!content) return;

    this.marketingService.improveMessage({ message: content, language: 'english' }).subscribe({
      next: (response) => {
        if (response.success && response.improvedMessage) {
          this.actionForm.patchValue({ content: response.improvedMessage });
        }
      },
      error: () => {
        this.snackBar.open('Failed to improve message', 'Close', { duration: 3000 });
      }
    });
  }
}