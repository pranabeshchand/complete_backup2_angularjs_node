'use strict';

module.exports = function(Contact) {
  Contact.status = function(ck){
    var mysql      = require('mysql');
    var connection = mysql.createConnection({
      host     : 'localhost',
      user     : 'root',
      password : 'mim',
      database : 'salesmysql'
    });
    var response = "";
    connection.connect(function(err){
      if(err){
        console.log('Error connecting to Db');
        return;
      }
        response = "Connection established";
      console.log('Connection established');
    });
    connection.query('SHOW TABLES LIKE Contact',function(err,rows){
      try{

        if(rows)
        console.log(rows);
        console.log('column received from Db:\n');
        var tab = "create table Contact(id int(10)primary key auto_increment, name varchar(100),email varchar(200))"
        connection.query(tab,function(err,rows){
          try{
            console.log('column received from Db:\n');
            console.log(rows);
            console.log('vvvvv '+err);
          }catch(err){
            console.log("error:- "+err);
          }

        });

      }catch(err){
        console.log(err);
      }



    });
    ck(null, response);
  };
  Contact.remoteMethod(
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
  Contact.contact = function(accept) {
    console.log(JSON.stringify(accept));
    Contact.create(accept,function(err, res){
      if(err){
        console.log(err);

      }else{
        console.log(JSON.stringify(res));
      }

    });

    //ck(null, 'Greetings... ' + accept);
  }

  Contact.remoteMethod('contact', {
    accepts: {arg: 'accept', type: 'object'},
    returns: {arg: 'ret', type: 'object'}
  });
};
