import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TopNavbarComponent } from './components/top-navbar/top-navbar.component';
import { SidebarComponent } from './components/sidebar/sidebar.component';
import { RouterModule } from '@angular/router';
import { NotFoundComponent } from './not-found/not-found.component';
import { ConfirmDialogComponent } from './components/confirm-dialog/confirm-dialog.component';
import { MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { BreadcrumbComponent } from './components/breadcrumb/breadcrumb.component';



@NgModule({
  declarations: [
    TopNavbarComponent,
    SidebarComponent,
    NotFoundComponent,
    ConfirmDialogComponent,
    BreadcrumbComponent,
  ],
  imports: [
    CommonModule,
    RouterModule,
    MatButtonModule,
    MatDialogModule
  ],
  exports: [
    TopNavbarComponent,
    SidebarComponent,
    BreadcrumbComponent,
    ConfirmDialogComponent,
    NotFoundComponent

  ],
})
export class SharedModule { }
