import { Component } from '@angular/core';

@Component({
  selector: 'my-app',
  templateUrl: `app/templates/home.component.html`,
})
export class AppComponent  {
  name: string;
  email: string;
  constructor(){
    this.name = "";
    this.email = "";
  }
  createRecord(){
    console.log(this.name+" "+this.email);
  }
}
