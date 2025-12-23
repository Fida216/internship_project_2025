import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { ActivationComponent } from './components/activation/activation.component';
import { ResetPasswordComponent } from './components/reset-password/reset-password.component';
import { RequestDemoComponent } from './components/request-demo/request-demo.component';

const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'reset-password', component: ResetPasswordComponent },
  { path: 'invitation', component: ActivationComponent },
  { path: 'request-demo', component: RequestDemoComponent }

];


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserRoutingModule { }
