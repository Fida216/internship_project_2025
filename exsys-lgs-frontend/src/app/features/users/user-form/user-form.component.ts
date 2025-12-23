// features/users/user-form/user-form.component.ts
import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { UserService } from '../services/user.services';
import { OfficesService } from '../../offices/services/offices.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-user-form',
  templateUrl: './user-form.component.html',
  standalone: false,
  styleUrls: ['./user-form.component.css']
})
export class UserFormComponent implements OnInit {
  userForm!: FormGroup;
  isLoading = false;
  offices: any[] = [];
  hidePassword = true;

  roles = [
    { value: 'agent', label: 'Agent' },
    { value: 'admin', label: 'Admin' }
  ];

  statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' }
  ];

  constructor(
    private fb: FormBuilder,
    private userService: UserService,
    private officesService: OfficesService,
    private dialogRef: MatDialogRef<UserFormComponent>,
    private snackBar: MatSnackBar,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadOffices();
  }

  initForm(): void {
    this.userForm = this.fb.group({
      firstName: ['', [Validators.required, Validators.maxLength(50)]],
      lastName: ['', [Validators.required, Validators.maxLength(50)]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.required, Validators.pattern(/^\+?[0-9\s\-\(\)]{8,20}$/)]],
      role: ['agent', Validators.required],
      password: ['', [Validators.required, Validators.minLength(6)]],
      exchangeOfficeId: [''],
      status: ['active', Validators.required]
    });

    // Make exchangeOfficeId required for agents
    this.userForm.get('role')?.valueChanges.subscribe(role => {
      const exchangeOfficeControl = this.userForm.get('exchangeOfficeId');
      if (role === 'agent') {
        exchangeOfficeControl?.setValidators([Validators.required]);
      } else {
        exchangeOfficeControl?.clearValidators();
      }
      exchangeOfficeControl?.updateValueAndValidity();
    });
  }

  loadOffices(): void {
    this.officesService.getExchangeOffices().subscribe({
      next: (response) => {
        this.offices = response.exchangeOffices;
      },
      error: () => {
        this.snackBar.open('Failed to load offices', 'Close', { duration: 3000 });
      }
    });
  }

  onSubmit(): void {
    if (this.userForm.invalid) {
      return;
    }

    this.isLoading = true;
    const userData = this.userForm.value;

    this.userService.createUser(userData).subscribe({
      next: () => {
        this.isLoading = false;
        this.snackBar.open('User created successfully', 'Close', { duration: 3000 });
        this.dialogRef.close(true);
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to create user', 'Close', { duration: 3000 });
      }
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }
}