    // features/clients/components/client-details/client-details.component.ts
    import { Component, OnInit } from '@angular/core';
    import { ActivatedRoute } from '@angular/router';
    import { ClientService } from '../../services/client.service';
    import { Client } from '../../models/client.model';
    import { Location } from '@angular/common';
    import { MatDialog } from '@angular/material/dialog';
    import { ClientFormComponent } from '../../client-form/client-form/client-form.component';
    import { MatSnackBar } from '@angular/material/snack-bar';

    @Component({
      selector: 'app-client-details',
      templateUrl: './client-details.component.html',
      standalone: false,
      styleUrls: ['./client-details.component.css']
    })
    export class ClientDetailsComponent implements OnInit {
      client: Client | null = null;
      isLoading = false;
      breadcrumbRootLabel: string = 'Clients';

      constructor(
        private route: ActivatedRoute,
        private clientService: ClientService,
        private location: Location,
        private dialog: MatDialog,
        private snackBar: MatSnackBar
      ) {}

      ngOnInit(): void {
        const myClientsFlag = this.route.snapshot.data['myClients'] || false;
        this.breadcrumbRootLabel = myClientsFlag ? 'My Clients' : 'Clients';

        this.loadClient();
        }

      loadClient(): void {
        const clientId = this.route.snapshot.paramMap.get('id');
        if (!clientId) return;

        this.isLoading = true;
        this.clientService.getClientDetails(clientId).subscribe({
          next: (client) => {
            this.client = client.client;
            this.isLoading = false;
          },
          error: () => {
            this.isLoading = false;
            this.snackBar.open('Failed to load client details', 'Close', { duration: 3000 });
            this.location.back();
          }
        });
      }

      openEditDialog(): void {
        if (!this.client) return;

        const dialogRef = this.dialog.open(ClientFormComponent, {
          width: '600px',
          disableClose: true,
          data: { client: this.client }
        });

        dialogRef.afterClosed().subscribe(result => {
          if (result) {
            this.loadClient();
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