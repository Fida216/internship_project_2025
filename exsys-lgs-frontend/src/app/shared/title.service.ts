import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TitleService {


  private title:string = 'Dashboard'

  private titleSource = new BehaviorSubject<string>(this.title);
  currentTitle = this.titleSource.asObservable();

  constructor() { }
  
  setTitle(newTitle : string ){
    this.title = newTitle;
    this.titleSource.next(this.title);
  }
}
