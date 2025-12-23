import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MarketingService } from '../services/marketing.service';
import { ClientService } from '../../clients/services/client.service';
import { Client } from '../../clients/models/client.model';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-campaign-form',
  templateUrl: './campaign-form.component.html',
  standalone: false,
  styleUrls: ['./campaign-form.component.css']
})
export class CampaignFormComponent implements OnInit {
  campaignForm!: FormGroup;
  isLoading = false;
  clients: Client[] = [];
  selectedClients: Client[] = [];
  isEditMode = false;

  statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'active', label: 'Active' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
  ];

  constructor(
    private fb: FormBuilder,
    private marketingService: MarketingService,
    private clientService: ClientService,
    private dialogRef: MatDialogRef<CampaignFormComponent>,
    private snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadClients();
    
    // Subscribe to targets form control changes
    this.campaignForm.get('targets')?.valueChanges.subscribe((selected: Client[]) => {
      this.selectedClients = selected || [];
    });
    
    if (this.data?.campaign) {
      this.isEditMode = true;
      this.patchForm(this.data.campaign);
    }
  }

  initForm(): void {
    this.campaignForm = this.fb.group({
      title: ['', [Validators.required, Validators.maxLength(100)]],
      description: ['', [Validators.required, Validators.maxLength(500)]],
      status: ['draft', Validators.required],
      startDate: ['', Validators.required],
      endDate: ['', Validators.required],
      targets: [[]]
    });
  }

  patchForm(campaign: any): void {
    this.campaignForm.patchValue({
      title: campaign.title,
      description: campaign.description,
      status: campaign.status,
      startDate: campaign.startDate,
      endDate: campaign.endDate
    });
    
    // If editing an existing campaign with targets, set them
    if (campaign.targetClients && campaign.targetClients.length > 0) {
      // We need to find the full client objects from our clients list
      const targetClientIds = campaign.targetClients.map((client: any) => client.id);
      const selected = this.clients.filter(client => targetClientIds.includes(client.id));
      this.campaignForm.get('targets')?.setValue(selected);
    }
  }

  loadClients(): void {
    this.clientService.getClients({ page: 1, limit: 100 }, true).subscribe({
      next: (response) => {
        this.clients = response.clients;
        
        // After loading clients, if we're in edit mode, patch the form again
        // to ensure targets are properly set
        if (this.data?.campaign) {
          this.patchForm(this.data.campaign);
        }
      },
      error: () => {
        this.snackBar.open('Failed to load clients', 'Close', { duration: 3000 });
      }
    });
  }

  onSubmit(): void {
    if (this.campaignForm.invalid) {
      return;
    }

    this.isLoading = true;
    const campaignData = this.campaignForm.value;
    
    // Format dates
    if (campaignData.startDate) {
      campaignData.startDate = this.formatDate(campaignData.startDate);
    }
    if (campaignData.endDate) {
      campaignData.endDate = this.formatDate(campaignData.endDate);
    }

    // Prepare targets - use the selectedClients array which is updated via valueChanges
    campaignData.targets = this.selectedClients.map(client => ({ clientId: client.id }));

    console.log('Submitting campaign data:', campaignData); // For debugging

    this.marketingService.createCampaign(campaignData).subscribe({
      next: () => {
        this.isLoading = false;
        this.snackBar.open('Campaign created successfully', 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: (error) => {
        this.isLoading = false;
        console.error('Error creating campaign:', error);
        this.snackBar.open('Failed to create campaign', 'Close', { duration: 3000 });
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  formatDate(date: any): string {
    if (date instanceof Date) {
      return date.toISOString().split('T')[0];
    }
    return date;
  }

  compareClients(client1: Client, client2: Client): boolean {
    return client1 && client2 ? client1.id === client2.id : client1 === client2;
  }

  // Helper method to remove a client from selection
  removeClient(client: Client): void {
    const currentTargets = this.campaignForm.get('targets')?.value || [];
    const updatedTargets = currentTargets.filter((c: Client) => c.id !== client.id);
    this.campaignForm.get('targets')?.setValue(updatedTargets);
  }
}