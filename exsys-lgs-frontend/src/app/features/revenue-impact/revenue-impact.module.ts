import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RevenueImpactRoutingModule } from './revenue-impact-routing.module';
import { RevenueImpactComponent } from './revenue-impact.component';


@NgModule({
  declarations: [
    RevenueImpactComponent
  ],
  imports: [
    CommonModule,
    RevenueImpactRoutingModule
  ]
})
export class RevenueImpactModule { }
