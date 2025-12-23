// features/clients/clients-routing.module.ts
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ClientListComponent } from './client-list/client-list/client-list.component';
import { ClientDetailsComponent } from './client-details/client-details/client-details.component';

const routes: Routes = [
  { path: '', component: ClientListComponent },
  { path: ':id', component: ClientDetailsComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ClientsRoutingModule { }