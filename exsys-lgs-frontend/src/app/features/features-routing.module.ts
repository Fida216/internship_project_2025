// features/features-routing.module.ts
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LayoutComponent } from './layout/layout.component';
import { AuthGuard } from '../core/guards/auth.guard';

const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    canActivate: [AuthGuard],
    children: [
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      { 
        path: 'dashboard', 
        loadChildren: () => import('./dashboard/dashboard.module').then(m => m.DashboardModule) 
      },
      { 
        path: 'users', 
        loadChildren: () => import('./users/users.module').then(m => m.UsersModule) 
      },

      { 
        path: 'my-clients', 
        loadChildren: () => import('./clients/clients.module').then(m => m.ClientsModule),
        data: { myClients: true }
      },
      { 
        path: 'clients', 
        loadChildren: () => import('./clients/clients.module').then(m => m.ClientsModule),
        data: { roles: ['admin'] }
      },
      { 
        path: 'offices', 
        loadChildren: () => import('./offices/offices.module').then(m => m.OfficesModule),
        data: { roles: ['admin'] }
      },
      { 
        path: 'transactions', 
        loadChildren: () => import('./transactions/transactions.module').then(m => m.TransactionsModule) 
      },
      { 
        path: 'segmentation', 
        loadChildren: () => import('./segmentation/segmentation.module').then(m => m.SegmentationModule) 
      },
      { 
        path: 'reports', 
        loadChildren: () => import('./reports/reports.module').then(m => m.ReportsModule),
        data: { roles: ['admin'] }
      },
      { 
        path: 'kpi', 
        loadChildren: () => import('./kpi/kpi.module').then(m => m.KpiModule),
        data: { roles: ['admin'] }
      },
      { 
        path: 'revenue-impact', 
        loadChildren: () => import('./revenue-impact/revenue-impact.module').then(m => m.RevenueImpactModule),
        data: { roles: ['admin'] }
      },
      {
        path: 'marketing',
        loadChildren: () => import('./marketing/marketing.module').then(m => m.MarketingModule),
        canActivate: [AuthGuard]
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FeaturesRoutingModule { }