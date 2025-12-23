// features/offices/offices-routing.module.ts
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { OfficeListComponent } from './office-list/office-list/office-list.component';
import { OfficeDetailsComponent } from './office-details/office-details.component';

const routes: Routes = [
  { path: '', component: OfficeListComponent },
  { path: ':id', component: OfficeDetailsComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OfficesRoutingModule { }