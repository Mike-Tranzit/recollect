import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'dateCoordinate'
})
export class DateCoordinatePipe implements PipeTransform {

  transform(value: any, args?: any): any {
    return null;
  }
}