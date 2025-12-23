// features/marketing/marketing.module.ts
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MarketingRoutingModule } from './marketing-routing.module';
import { CampaignListComponent } from './campaign-list/campaign-list.component';
import { CampaignDetailsComponent } from './campaign-details/campaign-details.component';
import { CampaignFormComponent } from './campaign-form/campaign-form.component';
import { MarketingActionFormComponent } from './marketing-action-form/marketing-action-form.component';
import { QuickMessageListComponent } from './quick-message-list/quick-message-list.component';
import { QuickMessageFormComponent } from './quick-message-form/quick-message-form.component';

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
import { MatCheckboxModule } from '@angular/material/checkbox';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

// Shared Modules
import { SharedModule } from '../../shared/shared.module';
import { QuickMessageDetailsComponent } from './quick-message-details/quick-message-details.component';

@NgModule({
  declarations: [
    CampaignListComponent,
    CampaignDetailsComponent,
    CampaignFormComponent,
    MarketingActionFormComponent,
    QuickMessageListComponent,
    QuickMessageFormComponent,
    QuickMessageDetailsComponent
  ],
  imports: [
    CommonModule,
    MarketingRoutingModule,
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
    MatTooltipModule,
    MatCheckboxModule
  ]
})
export class MarketingModule { }