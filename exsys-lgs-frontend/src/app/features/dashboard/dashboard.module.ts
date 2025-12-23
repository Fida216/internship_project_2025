import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardRoutingModule } from './dashboard-routing.module';
import { DashboardAdminComponent } from './components/dashboard-admin/dashboard-admin.component';
import { DashboardAgentComponent } from './components/dashboard-agent/dashboard-agent.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
  declarations: [
    DashboardAdminComponent,
    DashboardAgentComponent,
    DashboardComponent,
  ],
  imports: [
    CommonModule,
    DashboardRoutingModule,
    SharedModule
  ]
})
export class DashboardModule { }
