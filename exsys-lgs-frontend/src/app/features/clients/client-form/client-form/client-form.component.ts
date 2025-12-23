// features/clients/components/client-form/client-form.component.ts
import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ClientService } from '../../services/client.service';
import { Client } from '../../models/client.model';
import { Observable } from 'rxjs';
import { CountryService } from '../../../../core/services/country.service';
import { Country } from '../../../../core/models/country.model';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-client-form',
  templateUrl: './client-form.component.html',
  standalone: false,
  styleUrls: ['./client-form.component.css']
})
export class ClientFormComponent implements OnInit {
  clientForm!: FormGroup;
  isEditMode = false;
  isLoading = false;
  countries$: Observable<Country[]>;
  acquisitionSources = [
    { value: 'online', label: 'Online' },
    { value: 'walk_in', label: 'Walk-in' },
    { value: 'referral', label: 'Referral' },
    { value: 'phone_call', label: 'Phone Call' },
    { value: 'email', label: 'Email' },
    { value: 'social_media', label: 'Social Media' },
    { value: 'advertising', label: 'Advertising' },
    { value: 'partnership', label: 'Partnership' },
    { value: 'agent_direct', label: 'Agent Direct' },
    { value: 'other', label: 'Other' }
  ];
  genders = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' }
  ];

  constructor(
    private fb: FormBuilder,
    private clientService: ClientService,
    private countryService: CountryService,
    private dialogRef: MatDialogRef<ClientFormComponent>,
    private snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data: { client: Client }
  ) {
    this.countries$ = this.countryService.getCountries();
  }

  ngOnInit(): void {
    this.initForm();
    
    if (this.data?.client) {
      this.isEditMode = true;
      this.patchForm(this.data.client);
    }
  }

  initForm(): void {
    this.clientForm = this.fb.group({
      firstName: ['', [Validators.required, Validators.maxLength(100)]],
      lastName: ['', [Validators.required, Validators.maxLength(100)]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.required, Validators.pattern(/^\+?[0-9\s\-\(\)]{8,20}$/)]],
      whatsapp: ['', [Validators.pattern(/^\+?[0-9\s\-\(\)]{8,20}$/)]],
      birthDate: ['', Validators.required],
      nationality: ['', Validators.required],
      nationalId: [''],
      passport: [''],
      residence: ['', Validators.required],
      gender: ['', Validators.required],
      acquisitionSource: ['', Validators.required],
      currentSegment: ['']
    });
  }

  patchForm(client: Client): void {
    this.clientForm.patchValue({
      firstName: client.firstName,
      lastName: client.lastName,
      email: client.email,
      phone: client.phone,
      nationalId: client.nationalId || '',
      passport: client.passport || '',
      whatsapp: client.whatsapp || '',
      birthDate: client.birthDate,
      nationality: client.nationality,
      residence: client.residence || '',
      gender: client.gender || '',
      acquisitionSource: client.acquisitionSource || '',
      currentSegment: client.currentSegment || ''
    });
  }

  onSubmit(): void {
    if (this.clientForm.invalid) {
      return;
    }

    this.isLoading = true;
    const clientData = this.clientForm.value;

    if (clientData.birthDate && clientData.birthDate instanceof Date) {
      const d: Date = clientData.birthDate;
      if (!isNaN(d.getTime())) {
        clientData.birthDate = d.toISOString().split('T')[0];
      }
      // e.g. "1994-03-02"
    }
    
    console.log("Edit Mode " ,this.isEditMode )
    
  
      if (this.isEditMode) {
      if (clientData.nationalId === this.data.client.nationalId) {
      delete clientData.nationalId;
      }
      if (clientData.passport === this.data.client.passport) {
      delete clientData.passport;
      }
    }

    const operation = this.isEditMode 
      ? this.clientService.updateClient(this.data.client.id, clientData)
      : this.clientService.createClient(clientData);

    operation.subscribe({
      next: () => {
        this.isLoading = false;
        this.snackBar.open(`Client ${this.isEditMode ? 'updated' : 'created'} successfully`, 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open(`Failed to ${this.isEditMode ? 'update' : 'create'} client`, 'Close', { duration: 3000 });
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }
}