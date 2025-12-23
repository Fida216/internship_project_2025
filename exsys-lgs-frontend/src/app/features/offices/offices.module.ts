// features/offices/offices.module.ts
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OfficesRoutingModule } from './offices-routing.module';
import { OfficeListComponent } from './office-list/office-list/office-list.component';
import { OfficeFormComponent } from './office-form/office-form/office-form.component';
import { OfficeDetailsComponent } from './office-details/office-details.component';

// Angular Material Modules
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatSortModule } from '@angular/material/sort';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatChipsModule } from '@angular/material/chips';
import { MatCardModule } from '@angular/material/card';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatTooltipModule } from '@angular/material/tooltip';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

// Shared Modules
import { SharedModule } from '../../shared/shared.module';

@NgModule({
  declarations: [
    OfficeListComponent,
    OfficeFormComponent,
    OfficeDetailsComponent
  ],
  imports: [
    CommonModule,
    OfficesRoutingModule,
    ReactiveFormsModule,
    SharedModule,
    FormsModule,
    
    // Angular Material
    MatTableModule,
    MatPaginatorModule,
    MatSortModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatDialogModule,
    MatFormFieldModule,
    MatSelectModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatChipsModule,
    MatCardModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
    MatTooltipModule
  ]
})
export class OfficesModule { }