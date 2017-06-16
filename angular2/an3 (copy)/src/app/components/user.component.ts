import { Component } from '@angular/core';

@Component({
    moduleId: module.id,
    selector: 'user',
    templateUrl: './../../app/template/user.component.html',
})
//'./../../app/components/template/user.component.html',
export class UserComponent  {
    name:string;
    address: address;
    hobbies: string[];
    okk:string;
constructor(){
    this.name= "oye b";
    this.address = {
        street:'south city',
        city: 'gurugram'
    };
    this.hobbies = ["music",'dancing','surfing'];
}
     deletelist(i){
        this.hobbies.splice(i,1);
    }
    addHobbies(hobby){
        this.hobbies.push(hobby);
        this.okk = "";

     }
}
interface address{
    street:string;
    city:string;
}