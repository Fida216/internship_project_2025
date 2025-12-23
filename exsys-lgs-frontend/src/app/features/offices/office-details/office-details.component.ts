// features/offices/office-details/office-details.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { OfficesService } from '../services/offices.service';
import { ExchangeOffice } from '../models/office.model';
import { Location } from '@angular/common';
import { MatDialog } from '@angular/material/dialog';
import { OfficeFormComponent } from '../office-form/office-form/office-form.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-office-details',
  templateUrl: './office-details.component.html',
  standalone: false,
  styleUrls: ['./office-details.component.css']
})
export class OfficeDetailsComponent implements OnInit {
  office: ExchangeOffice | null = null;
  isLoading = false;
  isAdmin = false;

  constructor(
    private route: ActivatedRoute,
    private officesService: OfficesService,
    private location: Location,
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
    private authService: AuthService
  ) {
    this.isAdmin = this.authService.isAdmin();
  }

  ngOnInit(): void {
    this.loadOffice();
  }

  loadOffice(): void {
    const officeId = this.route.snapshot.paramMap.get('id');
    if (!officeId) return;

    this.isLoading = true;
    this.officesService.getExchangeOfficeDetails(officeId).subscribe({
      next: (response) => {
        this.office = response.exchangeOffice;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load office details', 'Close', { duration: 3000 });
        this.location.back();
      }
    });
  }

  openEditDialog(): void {
    if (!this.office) return;

    const dialogRef = this.dialog.open(OfficeFormComponent, {
      width: '600px',
      disableClose: true,
      data: { office: this.office }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadOffice();
      }
    });
  }

  goBack(): void {
    this.location.back();
  }

  getStatusColor(status: string): string {
    return status === 'active' ? 'primary' : 'warn';
  }
}