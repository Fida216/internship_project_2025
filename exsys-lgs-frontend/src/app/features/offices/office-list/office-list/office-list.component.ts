// features/offices/office-list/office-list.component.ts
import { Component, OnInit } from '@angular/core';
import { OfficesService } from '../../services/offices.service';
import { ExchangeOffice } from '../../models/office.model';
import { PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { OfficeFormComponent } from '../../office-form/office-form/office-form.component';
import { ConfirmDialogComponent } from '../../../../shared/components/confirm-dialog/confirm-dialog.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService } from '../../../../core/services/auth.service';

@Component({
  selector: 'app-office-list',
  templateUrl: './office-list.component.html',
  standalone: false,
  styleUrls: ['./office-list.component.css']
})
export class OfficeListComponent implements OnInit {
  offices: ExchangeOffice[] = [];
  totalOffices = 0;
  pageSize = 10;
  pageIndex = 0;
  isLoading = false;
  statusFilter = '';
  isAdmin = false;

  displayedColumns: string[] = [
    'name',
    'email',
    'phone',
    'owner',
    'status',
    'createdAt',
    'actions'
  ];

  constructor(
    private officesService: OfficesService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
    private authService: AuthService
  ) {
    this.isAdmin = this.authService.isAdmin();
  }

  ngOnInit(): void {
    this.loadOffices();
  }

  loadOffices(): void {
    this.isLoading = true;
    const params: any = {
      page: this.pageIndex + 1,
      limit: this.pageSize
    };

    if (this.statusFilter) {
      params.status = this.statusFilter;
    }

    this.officesService.getExchangeOffices(params).subscribe({
      next: (response: { exchangeOffices: ExchangeOffice[]; total: number }) => {
        this.offices = response.exchangeOffices;
        this.totalOffices = response.total;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load offices', 'Close', { duration: 3000 });
      }
    });
  }

  onPageChange(event: PageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.loadOffices();
  }

  onFilterChange(): void {
    this.pageIndex = 0;
    this.loadOffices();
  }

  openCreateDialog(): void {
    const dialogRef = this.dialog.open(OfficeFormComponent, {
      width: '600px',
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadOffices();
      }
    });
  }

  openEditDialog(office: ExchangeOffice): void {
    const dialogRef = this.dialog.open(OfficeFormComponent, {
      width: '600px',
      disableClose: true,
      data: { office }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadOffices();
      }
    });
  }

  deleteOffice(officeId: string): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Delete Office',
        message: 'Are you sure you want to delete this office? This action cannot be undone.'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.isLoading = true;
        this.officesService.deleteOffice(officeId).subscribe({
          next: () => {
            this.loadOffices();
            this.snackBar.open('Office deleted successfully', 'Close', { duration: 3000 });
          },
          error: () => {
            this.isLoading = false;
            this.snackBar.open('Failed to delete office', 'Close', { duration: 3000 });
          }
        });
      }
    });
  }

  updateOfficeStatus(officeId: string, currentStatus: string): void {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    this.isLoading = true;
    this.officesService.updateOfficeStatus(officeId, newStatus).subscribe({
      next: () => {
        this.loadOffices();
        this.snackBar.open(`Office status updated to ${newStatus}`, 'Close', { duration: 3000 });
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to update office status', 'Close', { duration: 3000 });
      }
    });
  }

  getStatusColor(status: string): string {
    return status === 'active' ? 'primary' : 'warn';
  }
}