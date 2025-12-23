import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SegmentationComponent } from './segmentation.component';

const routes: Routes = [{ path: '', component: SegmentationComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SegmentationRoutingModule { }
