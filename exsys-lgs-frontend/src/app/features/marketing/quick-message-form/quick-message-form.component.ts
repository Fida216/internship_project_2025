// features/marketing/quick-message-form/quick-message-form.component.ts
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';
import { MarketingService } from '../services/marketing.service';
import { ClientService } from '../../clients/services/client.service';
import { Client } from '../../clients/models/client.model';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-quick-message-form',
  templateUrl: './quick-message-form.component.html',
  standalone: false,
  styleUrls: ['./quick-message-form.component.css']
})
export class QuickMessageFormComponent implements OnInit {
  quickMessageForm!: FormGroup;
  isLoading = false;
  clients: Client[] = [];
  selectedClients: Client[] = [];

  channelTypes = [
    { value: 'email', label: 'Email' },
    { value: 'sms', label: 'SMS' },
    { value: 'whatsapp', label: 'WhatsApp' },
    { value: 'push', label: 'Push Notification' }
  ];

  constructor(
    private fb: FormBuilder,
    private marketingService: MarketingService,
    private clientService: ClientService,
    private dialogRef: MatDialogRef<QuickMessageFormComponent>,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadClients();
  }

  initForm(): void {
    this.quickMessageForm = this.fb.group({
      title: ['', [Validators.required, Validators.maxLength(100)]],
      channelType: ['email', Validators.required],
      content: ['', [Validators.required, Validators.maxLength(1000)]],
      targets: [[]]
    });

    this.quickMessageForm.get('targets')?.valueChanges.subscribe((selected: Client[]) => {
    this.selectedClients = selected;
  });
  }

  loadClients(): void {
    this.clientService.getClients({ page: 1, limit: 100 }, true).subscribe({
      next: (response) => {
        this.clients = response.clients;
      },
      error: () => {
        this.snackBar.open('Failed to load clients', 'Close', { duration: 3000 });
      }
    });
  }

  onSubmit(): void {
    if (this.quickMessageForm.invalid) {
      return;
    }

    this.isLoading = true;
    const messageData = this.quickMessageForm.value;
    
    // Prepare targets
    messageData.targets = this.selectedClients.map(client => ({ clientId: client.id }));

    this.marketingService.createQuickMessage(messageData).subscribe({
      next: () => {
        this.isLoading = false;
        this.snackBar.open('Quick message sent successfully', 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to send quick message', 'Close', { duration: 3000 });
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  improveMessage(): void {
    const content = this.quickMessageForm.get('content')?.value;
    if (!content) return;

    this.marketingService.improveMessage({ message: content, language: 'english' }).subscribe({
      next: (response) => {
        if (response.success && response.improvedMessage) {
          this.quickMessageForm.patchValue({ content: response.improvedMessage });
        }
      },
      error: () => {
        this.snackBar.open('Failed to improve message', 'Close', { duration: 3000 });
      }
    });
  }

  removeClient(clientToRemove: Client): void {
  // Remove from selected clients array
  this.selectedClients = this.selectedClients.filter(client => client.id !== clientToRemove.id);
  
  // Update the form control value
  const currentTargets = this.quickMessageForm.get('targets')?.value || [];
  const updatedTargets = currentTargets.filter((client: Client) => client.id !== clientToRemove.id);
  this.quickMessageForm.patchValue({ targets: updatedTargets });
}

  compareClients(client1: Client, client2: Client): boolean {
    return client1 && client2 ? client1.id === client2.id : client1 === client2;
  }
}