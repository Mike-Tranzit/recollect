import {Pipe, PipeTransform} from '@angular/core';

@Pipe({
    name: 'searchPlate'
})
export class SearchPlatePipe implements PipeTransform {

    transform(items: any[], filter: string): any {
        if (!filter || filter.length < 2) {
            return items;
        }
        return items.filter(item => item.plate.toLowerCase().indexOf(filter.toLowerCase()) !== -1);
    }
}