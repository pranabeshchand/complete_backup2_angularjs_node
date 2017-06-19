var express = require('express');
var router = express.Router();
var mongojs = require('mongojs');
var db = mongojs('mongodb://localhost/mytasklist',['tasks']);
//console.log(db);
//Get all Tasks
router.get('/tasks', function(req, res, next){
	db.tasks.find(function(err,tasks){
		if(err){
			res.send(err);
		}
		console.log(tasks);
		res.json(tasks);
	});
});

//Get single tasks
router.get('/tasks/:id', function(req, res, next){
	db.tasks.findOne({_id:mongojs.ObjectId(req.params.id)},function(err,task){
		if(err){
			res.send(err);
		}
		console.log(task);
		res.json(task);
	});
});

//Save Tasks

router.post('/task', function(req, res, next){
	var task = req.body;
	if(!task.title || (task.isDone + '')){
		res.status(400);
		res.json({
			'error': 'bad Data'
		});
	}else{
	db.tasks.save(task, function(err, task){
		if(err){
			res.send(err);
		}
		res.json(task);
	});
}
});

//Delete single tasks
router.delete('/tasks/:id', function(req, res, next){
	db.tasks.remove({_id:mongojs.ObjectId(req.params.id)},function(err,task){
		if(err){
			res.send(err);
		}
		console.log(task);
		res.json(task);
	});
});

//Update single tasks
router.put('/tasks/:id', function(req, res, next){
	var task =  req.body;
	var updTask = {};
	if(task.isDone){
		updTask.isDone = task.isDone;
	}
	if(task.title){
		updTask.title = task.title;
	}
	if(!updTask){
		res.status(400);
		res.json({
			'error':'bad Data'
		});
	}else{
		db.tasks.update({_id:mongojs.ObjectId(req.params.id)},updTask,{},function(err,task){
			if(err){
				res.send(err);
			}
 			res.json(task);
		});
	}

});

module.exports = router;