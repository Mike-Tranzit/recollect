import {Component, OnInit, Input} from '@angular/core';

@Component({
    selector: 'app-menu',
    templateUrl: './menu.component.html',
    styleUrls: ['./menu.component.css']
})
export class MenuComponent implements OnInit {
    @Input('title') title: string;
    public computer = false;
    constructor() {
        if (window.screen.width > 1500) this.computer = true;
    }

    ngOnInit() {
    }

    public menu(event: Event): void {
        const x = document.getElementById('myTopnav');
        if (x.className === 'topnav') {
            x.className += ' responsive';
        } else {
            x.className = 'topnav';
        }
        event.preventDefault();
    }
}