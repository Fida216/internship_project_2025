// features/offices/office-form/office-form.component.ts
import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { OfficesService } from '../../services/offices.service';
import { ExchangeOffice } from '../../models/office.model';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-office-form',
  templateUrl: './office-form.component.html',
  standalone: false,
  styleUrls: ['./office-form.component.css']
})
export class OfficeFormComponent implements OnInit {
  officeForm!: FormGroup;
  isEditMode = false;
  isLoading = false;

  constructor(
    private fb: FormBuilder,
    private officesService: OfficesService,
    private dialogRef: MatDialogRef<OfficeFormComponent>,
    private snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data: { office: ExchangeOffice }
  ) {}

  ngOnInit(): void {
    this.initForm();
    
    if (this.data?.office) {
      this.isEditMode = true;
      this.patchForm(this.data.office);
    }
  }

  initForm(): void {
    this.officeForm = this.fb.group({
      name: ['', [Validators.required, Validators.maxLength(100)]],
      address: ['', [Validators.required, Validators.maxLength(200)]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.required, Validators.pattern(/^\+?[0-9\s\-\(\)]{8,20}$/)]],
      owner: ['', [Validators.required, Validators.maxLength(100)]],
      status: ['active', Validators.required]
    });
  }

  patchForm(office: ExchangeOffice): void {
    this.officeForm.patchValue({
      name: office.name,
      address: office.address,
      email: office.email,
      phone: office.phone,
      owner: office.owner,
      status: office.status
    });
  }

  onSubmit(): void {
    if (this.officeForm.invalid) {
      return;
    }

    this.isLoading = true;
    const officeData = this.officeForm.value;

    const operation = this.isEditMode 
      ? this.officesService.updateOffice(this.data.office.id, officeData)
      : this.officesService.createOffice(officeData);

    operation.subscribe({
      next: () => {
        this.isLoading = false;
        this.snackBar.open(`Office ${this.isEditMode ? 'updated' : 'created'} successfully`, 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open(`Failed to ${this.isEditMode ? 'update' : 'create'} office`, 'Close', { duration: 3000 });
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }
}