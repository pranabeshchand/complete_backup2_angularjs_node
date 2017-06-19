import { Component } from '@angular/core';
import {TaskService} from './services/task.service';
import {TasksComponent } from './components/tasks/tasks.component'

@Component({
    moduleId: module.id,
    selector: 'my-app',
    templateUrl: '../app/app.component.html',
    providers: [TaskService]
})
export class AppComponent { }