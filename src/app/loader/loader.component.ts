import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-loader',
  templateUrl: './loader.component.html',
  styleUrls: ['./loader.component.css']
})
export class LoaderComponent implements OnInit {
  public loadSrc: string;
  constructor() { }

  @Input('active') public active: boolean;

  ngOnInit() {
    this.loadSrc = 'load' + Math.floor( Math.random() * (15 - 0) ) + '.gif';
  }
}