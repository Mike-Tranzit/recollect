import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ModalComponent } from '../_components/modal/modal.component';
import { LoaderComponent } from '../loader/loader.component';
@NgModule({
  imports: [
    CommonModule
  ],
  exports: [ModalComponent, LoaderComponent],
  declarations: [ModalComponent, LoaderComponent]
})
export class GeneralModule { }