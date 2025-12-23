// features/users/users.component.ts
import { Component, OnInit } from '@angular/core';
import { UserService } from './services/user.services';
import { User, ExchangeOfficeWithAgents } from './models/user.model';
import { MatDialog } from '@angular/material/dialog';
import { UserFormComponent } from './user-form/user-form.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService } from '../../core/services/auth.service';
import { OfficesService } from '../offices/services/offices.service';

@Component({
  selector: 'app-users',
  templateUrl: './users.component.html',
  standalone: false,
  styleUrls: ['./users.component.css']
})
export class UsersComponent implements OnInit {
  users: User[] = [];
  groupedAgents: ExchangeOfficeWithAgents[] = [];
  isLoading = false;
  viewMode: 'list' | 'grouped' = 'list';
  isAdmin = false;

  constructor(
    private userService: UserService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
    private authService: AuthService,
    private officesService: OfficesService
  ) {
    this.isAdmin = this.authService.isAdmin();
  }

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.isLoading = true;
    this.userService.getUsers().subscribe({
      next: (users) => {
        this.users = users;
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load users', 'Close', { duration: 3000 });
      }
    });
  }

  loadGroupedAgents(): void {
    this.isLoading = true;
    this.userService.getAgentsGroupedByOffice().subscribe({
      next: (groupedData) => {
        this.groupedAgents = groupedData;
        this.viewMode = 'grouped';
        this.isLoading = false;
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to load agents', 'Close', { duration: 3000 });
      }
    });
  }

  openCreateDialog(): void {
    const dialogRef = this.dialog.open(UserFormComponent, {
      width: '600px',
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadUsers();
      }
    });
  }

  updateUserStatus(user: User): void {
    const newStatus = user.status === 'active' ? 'inactive' : 'active';
    
    this.isLoading = true;
    this.userService.updateUserStatus(user.id, { status: newStatus }).subscribe({
      next: () => {
        this.loadUsers();
        this.snackBar.open(`User status updated to ${newStatus}`, 'Close', { duration: 3000 });
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Failed to update user status', 'Close', { duration: 3000 });
      }
    });
  }

  getStatusColor(status: string): string {
    return status === 'active' ? 'primary' : 'warn';
  }

  getRoleColor(role: string): string {
    return role === 'admin' ? 'accent' : 'primary';
  }

  switchView(mode: 'list' | 'grouped'): void {
    this.viewMode = mode;
    if (mode === 'grouped') {
      this.loadGroupedAgents();
    } else {
      this.loadUsers();
    }
  }
}