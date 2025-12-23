import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SegmentationRoutingModule } from './segmentation-routing.module';
import { SegmentationComponent } from './segmentation.component';


@NgModule({
  declarations: [
    SegmentationComponent
  ],
  imports: [
    CommonModule,
    SegmentationRoutingModule
  ]
})
export class SegmentationModule { }
