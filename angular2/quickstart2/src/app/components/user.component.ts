import { Component } from '@angular/core';

@Component({
  selector:'user',
  templateUrl: "./../../template/list.html",
})
export class UserComponent{
  name: string;
  hobbies: string[];
  showHobbies: boolean;
  constructor(){
    this.name = 'Pranabesh';
    this.hobbies = ['a','b','c','d'];
    this.showHobbies = false;

  }
  deleteHobby(i){
    this.hobbies.splice(i,1);
  }
  addHobby(hobby){
    this.hobbies.push(hobby);
    console.log(hobby);
  }
  toggleHobbies(){
    if(this.showHobbies == true){
      this.showHobbies = false;
    }else{
      this.showHobbies = true;
    }
  }
}
