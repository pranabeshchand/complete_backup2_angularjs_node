 var app = require('../server');

 var tasks = [
 {
 name:'pj',
 a: 'demo',

 }
 ];

 // this loads the accountDb configuration in ~/server/datasources.json
 var dataSource = app.dataSources.mysqlDs;

 // this automigrates the Account model
 dataSource.automigrate('Sales', function(err) {
 if (err) throw err;

 // this loads the Account model from ~/common/models/Account.json
 var Task = app.models.Sales;
 var count = tasks.length;
   tasks.forEach(function(sales) {
 // insert new records into the Account table
     Task.create(sales, function(err, record) {
 if (err) return console.log('------', err);

 console.log('Record created:', record);

 count--;

 if (count === 0) {
 console.log('done');
 dataSource.disconnect();
 }
 });
 });
 });
