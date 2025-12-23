import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { RevenueImpactComponent } from './revenue-impact.component';

const routes: Routes = [{ path: '', component: RevenueImpactComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RevenueImpactRoutingModule { }
