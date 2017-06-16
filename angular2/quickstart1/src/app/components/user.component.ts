import { Component } from '@angular/core';
import {PostsService} from '../services/posts.service';
 @Component({
   moduleId: 'module.id',
  selector: 'user',
  templateUrl: './user.component.html',
   providers: [PostsService]
})
export class UserComponent  {
  name: string;
  email: string;
  address: address;
  hobbies: string[];
  showHobbies: boolean;
   posts:Post[];
  constructor(private postsService: PostsService){
    this.name = 'pranabesh chand';
    this.email = "pchand@mindinmotion.co";
    this.address = {
      street: 'bc',
      city: 'delhi',
      state: 'Delhi',
      country: 'India'
    }
    this.hobbies = ['Music', 'Movies', 'Sports'];
    this.showHobbies = false;
    this.postsService.getPosts().subscribe(
      posts => {
        this.posts =posts
      }
    );
    console.log("constructor ran");
  }
  toggleHobbies(){
    if(this.showHobbies == true){
      this.showHobbies = false;
    }else{
      this.showHobbies = true;
    }

  }
  addHobby(hobby){
    this.hobbies.push(hobby);
    console.log(hobby);
  }
  deleteHobby(i){
    this.hobbies.splice(i,1);
  }
}
interface address{
  street: string;
  city: string;
  state: string;
  country: string
}
interface Post{
  id: number,
  title: string,
  body: string
}
