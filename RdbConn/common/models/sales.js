'use strict';

module.exports = function(Sales) {
  Sales.status = function(cb) {
    var mysql      = require('mysql');
    var connection = mysql.createConnection({
      host     : 'localhost',
      user     : 'root',
      password : 'mim',
      database : 'salesmysql'
    });
    connection.connect(function(err){
      if(err){
        console.log('Error connecting to Db');
        return;
      }
      console.log('Connection established');
    });
    connection.query('SELECT * FROM Sales',function(err,rows){
      if(err) throw err;

      console.log('Data received from Db:\n');
      console.log(rows);
    });
    connection.query('desc Sales',function(err,rows){
      if(err) throw err;

      console.log('column received from Db:\n');
      console.log(rows);
    });
    var currentDate = new Date();
    var currentHour = currentDate.getHours();
    var OPEN_HOUR = 6;
    var CLOSE_HOUR = 20;
    console.log('Current hour is %d', currentHour);
    var response;
    if (currentHour > OPEN_HOUR && currentHour < CLOSE_HOUR) {
      response = 'We are open for business.';
    } else {
      response = 'Sorry, we are closed. Open daily from 6am to 8pm.';
    }
    cb(null, response);
  };
  Sales.remoteMethod(
    'status', {
      http: {
        path: '/status',
        verb: 'get'
      },
      returns: {
        name: 'status',
        a: 'string'
      }
    }
  );
};
