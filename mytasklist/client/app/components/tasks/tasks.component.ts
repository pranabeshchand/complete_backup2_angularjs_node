import { Component } from '@angular/core';
import { TaskService } from '../../services/task.service';
import { Task } from '../../../Task';
@Component({
    moduleId: module.id,
    selector: 'tasks',
    templateUrl: `../../../app/components/tasks/tasks.component.html`
})
export class TasksComponent {
    tasks : Task[];
    title: string;

constructor(private taskService: TaskService){
this.taskService.getTasks().subscribe(tasks => {
    this.tasks = tasks;
   //console.log(tasks);
});
 }
    addTask(event: any){
        alert('called...')
        event.preventDefault();
        console.log(this.title);
    }
}