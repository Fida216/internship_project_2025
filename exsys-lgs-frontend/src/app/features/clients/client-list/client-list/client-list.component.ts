// features/clients/components/client-list/client-list.component.ts
import { Component, OnInit } from '@angular/core';
import { ClientService } from '../../services/client.service';
import { Client } from '../../models/client.model';
import { PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { ClientFormComponent } from '../../client-form/client-form/client-form.component';
import { ConfirmDialogComponent } from '../../../../shared/components/confirm-dialog/confirm-dialog.component';
import { ActivatedRoute, Router } from '@angular/router';
import { MatSnackBar } from '@angular/material/snack-bar';
import { CountryService } from '../../../../core/services/country.service';
import { Country } from '../../../../core/models/country.model';
import { OfficesService } from '../../../offices/services/offices.service';

@Component({
  selector: 'app-client-list',
  templateUrl: './client-list.component.html',
  standalone: false,
  styleUrls: ['./client-list.component.css']
})
export class ClientListComponent implements OnInit {
  countries: Country[] = [];

  // props
  clients: Client[] = [];
  totalClients = 0;
  pageSize = 10;
  pageIndex = 0;
  isLoading = false;
  searchQuery = '';
  statusFilter = '';
  nationalityFilter = '';
  genderFilter = '';
  isMyClients = false;
  exchangeOffices: any[] = [];
  selectedOfficeId: string = '';

displayedColumns: string[] = [
  'name',
  'email',
  'phone',
  'birthDate',
  'nationality',
  'residence',
  'acquisitionSource',
  'exchangeOffice',
  'status',
  'actions'
  ];
  
  constructor(
    private clientService: ClientService,
    private dialog: MatDialog,
    private route: ActivatedRoute,
    private snackBar: MatSnackBar,
    private router: Router,
    private countryService: CountryService,
    private officesService: OfficesService
  ) {}

  ngOnInit(): void {
    this.isMyClients = this.route.snapshot.data['myClients'] || false;
    this.loadCountries();
    if (!this.isMyClients) {
    this.loadExchangeOffices(); // only admins
    }
    this.loadClients();
  }


  loadExchangeOffices(): void {
  // will be replaced by a dedicated service later
  this.officesService.getExchangeOffices().subscribe({
      next: (data) => this.exchangeOffices = data.exchangeOffices,
      error: () => {
        this.snackBar.open('Failed to load exchange offices', 'Close', { duration: 3000 });
      }
    });
  }

  onOfficeChange(): void {
    this.pageIndex = 0;
    this.loadClients();
  }

  loadCountries(): void {
    this.countryService.getCountries().subscribe({
      next: (data) => (this.countries = data),
      error: () => {
        this.snackBar.open('Failed to load countries', 'Close', { duration: 3000 });
      }
    });
  }

  loadClients(): void {
    this.isLoading = true;
    const params = {
      page: this.pageIndex + 1,
      limit: this.pageSize,
      search: this.searchQuery,
      status: this.statusFilter,
      nationality: this.nationalityFilter,
      gender: this.genderFilter
    };

    if (this.isMyClients) {
      this.clientService.getClients(params, true).subscribe({
        next: (response) => {
          this.clients = response.clients;
          this.totalClients = response.total;
          this.isLoading = false;
        },
        error: () => this.isLoading = false
      });
    } else if (this.selectedOfficeId) {
      this.clientService.getClientsByOffice(params, this.selectedOfficeId).subscribe({
        next: (response) => {
          this.clients = response.clients;
          this.totalClients = response.total;
          this.isLoading = false;
        },
        error: () => this.isLoading = false
      });
    } else {
      this.clientService.getClientsGrouped().subscribe({
        next: (response) => {
          // adapt this part since grouped returns exchangeOffices[] with clients inside
          this.clients = response.exchangeOffices.flatMap((office: any) => office.clients);
          this.totalClients = response.totalClientsAcrossAllOffices;
          this.isLoading = false;
        },
        error: () => this.isLoading = false
      });
    }
  }

  onPageChange(event: PageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.loadClients();
  }

  onSearch(): void {
    this.pageIndex = 0;
    this.loadClients();
  }

  onFilterChange(): void {
    this.pageIndex = 0;
    this.loadClients();
  }

  openCreateDialog(): void {
    const dialogRef = this.dialog.open(ClientFormComponent, {
      width: '600px',
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadClients();
      }
    });
  }

  openEditDialog(client: Client): void {
    const dialogRef = this.dialog.open(ClientFormComponent, {
      // width: '600px',
      // panelClass: 'transparent-dialog',
      disableClose: true,
      data: { client }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadClients();
      }
    });
  }


  goToDetails(client: any) {
  this.router.navigate([client.id], { relativeTo: this.route }); 
  }
  

  deleteClient(clientId: string): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Delete Client',
        message: 'Are you sure you want to delete this client?'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.isLoading = true;
        this.clientService.deleteClient(clientId).subscribe({
          next: () => {
            this.loadClients();
            this.snackBar.open('Client deleted successfully', 'Close', { duration: 3000 });
          },
          error: () => {
            this.isLoading = false;
            this.snackBar.open('Failed to delete client', 'Close', { duration: 3000 });
          }
        });
      }
    });
  }

  getStatusColor(status: string): string {
    return status === 'active' ? 'primary' : 'warn';
  }
}